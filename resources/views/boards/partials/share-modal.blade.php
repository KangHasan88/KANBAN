<!-- Share Board Modal -->
<div id="shareModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Share Board: {{ $board->name }}</h3>
            <button onclick="closeShareModal()" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        
        <!-- Form Add User -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium mb-3">Add User to Board</h4>
            <div class="flex flex-col md:flex-row gap-3">
                <select id="shareUserId" class="flex-1 border rounded px-3 py-2">
                    <option value="">Select user...</option>
                    @php
                        $users = App\Models\User::where('id', '!=', auth()->id())
                            ->whereNotIn('id', $board->sharedUsers->pluck('id'))
                            ->get();
                    @endphp
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                    @endforeach
                </select>
                <select id="sharePermission" class="w-36 border rounded px-3 py-2">
                    <option value="view">👁️ View Only</option>
                    <option value="edit">✏️ Can Edit</option>
                </select>
                <button onclick="shareBoard()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Share
                </button>
            </div>
        </div>
        
        <!-- List Shared Users -->
        <div>
            <h4 class="font-medium mb-3">People with Access</h4>
            <div class="space-y-2">
                <!-- Owner -->
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($board->owner->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium">{{ $board->owner->name }}</p>
                            <p class="text-sm text-gray-500">{{ $board->owner->username }} (Owner)</p>
                        </div>
                    </div>
                    <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">Full Access</span>
                </div>
                
                <!-- Shared Users -->
                @foreach($board->sharedUsers as $sharedUser)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg" id="user-{{ $sharedUser->id }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($sharedUser->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium">{{ $sharedUser->name }}</p>
                            <p class="text-sm text-gray-500">{{ $sharedUser->username }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select onchange="updatePermission({{ $sharedUser->id }}, this.value)" 
                                class="border rounded px-2 py-1 text-sm">
                            <option value="view" {{ $sharedUser->pivot->permission == 'view' ? 'selected' : '' }}>
                                👁️ View Only
                            </option>
                            <option value="edit" {{ $sharedUser->pivot->permission == 'edit' ? 'selected' : '' }}>
                                ✏️ Can Edit
                            </option>
                        </select>
                        <button onclick="unshareUser({{ $sharedUser->id }})" 
                                class="text-red-600 hover:text-red-800 text-sm px-2 py-1">
                            🗑️ Remove
                        </button>
                    </div>
                </div>
                @endforeach
                
                @if($board->sharedUsers->count() === 0)
                <p class="text-gray-500 text-center py-4">No users shared yet</p>
                @endif
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeShareModal()" class="px-4 py-2 bg-gray-300 rounded">Close</button>
        </div>
    </div>
</div>

<script>
function openShareModal() {
    document.getElementById('shareModal').classList.remove('hidden');
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
}

function shareBoard() {
    const userId = document.getElementById('shareUserId').value;
    const permission = document.getElementById('sharePermission').value;
    
    if (!userId) {
        alert('Please select a user');
        return;
    }
    
    fetch('{{ route("boards.share", $board) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            permission: permission
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to share board');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function unshareUser(userId) {
    if (!confirm('Remove this user\'s access to this board?')) return;
    
    fetch('{{ route("boards.unshare", [$board, '__USER_ID__']) }}'.replace('__USER_ID__', userId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to remove user');
        }
    });
}

function updatePermission(userId, permission) {
    fetch('{{ route("boards.update-permission", [$board, '__USER_ID__']) }}'.replace('__USER_ID__', userId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ permission: permission })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Failed to update permission');
            location.reload();
        }
    });
}
</script>