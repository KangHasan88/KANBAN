@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold" style="color: #1e3a5f;">My Dashboard</h1>
            <p class="text-gray-500 mt-1">Manage all your projects in one place</p>
        </div>
        <button onclick="openCreateBoardModal()" class="btn-accent flex items-center gap-2">
            <span class="text-lg">+</span> New Board
        </button>
    </div>

    @if($ownedBoards->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2" style="color: #1e3a5f;">
            <span>📌</span> My Boards
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($ownedBoards as $board)
            <div class="board-card group relative">
                <div class="board-card-accent"></div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <a href="{{ route('boards.show', $board) }}" class="flex-1">
                            <h3 class="text-lg font-semibold mb-1" style="color: #1e3a5f;">{{ $board->name }}</h3>
                        </a>
                        <!-- Tombol Edit -->
                        <button onclick="openEditBoardModal({{ $board->id }}, '{{ addslashes($board->name) }}', '{{ addslashes($board->description) }}')" 
                                class="opacity-0 group-hover:opacity-100 transition text-gray-500 hover:text-blue-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('boards.show', $board) }}">
                        <p class="text-gray-500 text-sm">{{ $board->description ?? 'No description' }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-gray-400">{{ $board->lists->count() }} lists</span>
                            @if($board->sharedUsers->count() > 0)
                            <span class="text-xs px-2 py-1 rounded-full" style="background-color: #fef3c7; color: #d97706;">
                                🔗 Shared with {{ $board->sharedUsers->count() }}
                            </span>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($sharedBoards->count() > 0)
    <div>
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2" style="color: #1e3a5f;">
            <span>🔗</span> Shared with Me
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($sharedBoards as $board)
            <div class="board-card">
                <div class="board-card-primary"></div>
                <a href="{{ route('boards.show', $board) }}" class="block p-5">
                    <h3 class="text-lg font-semibold mb-2" style="color: #1e3a5f;">{{ $board->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ $board->description ?? 'No description' }}</p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-xs text-gray-400">{{ $board->lists->count() }} lists</span>
                        <span class="text-xs px-2 py-1 rounded-full" style="background-color: #e0e7ff; color: #1e3a5f;">
                            👤 Owner: {{ $board->owner->name }}
                        </span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($ownedBoards->count() === 0 && $sharedBoards->count() === 0)
    <div class="text-center py-16">
        <div class="text-6xl mb-4">🎯</div>
        <p class="text-gray-500 mb-4">You don't have any boards yet.</p>
        <button onclick="openCreateBoardModal()" class="btn-accent">
            Create your first board
        </button>
    </div>
    @endif
</div>

<!-- Modal Create Board -->
<div id="createBoardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border-0 w-96 shadow-xl rounded-xl bg-white">
        <h3 class="text-xl font-semibold mb-4" style="color: #1e3a5f;">Create New Board</h3>
        <form action="{{ route('boards.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Board Name" required 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            <textarea name="description" placeholder="Description (optional)" rows="3" 
                      class="w-full border border-gray-300 rounded-xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCreateBoardModal()" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="btn-accent">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Board -->
<div id="editBoardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border-0 w-96 shadow-xl rounded-xl bg-white">
        <h3 class="text-xl font-semibold mb-4" style="color: #1e3a5f;">Edit Board</h3>
        <form id="editBoardForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editBoardId" name="board_id">
            <input type="text" id="editBoardName" name="name" placeholder="Board Name" required 
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            <textarea id="editBoardDescription" name="description" placeholder="Description (optional)" rows="3" 
                      class="w-full border border-gray-300 rounded-xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditBoardModal()" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateBoardModal() {
    document.getElementById('createBoardModal').classList.remove('hidden');
}
function closeCreateBoardModal() {
    document.getElementById('createBoardModal').classList.add('hidden');
}

// Edit Board Functions
function openEditBoardModal(id, name, description) {
    document.getElementById('editBoardId').value = id;
    document.getElementById('editBoardName').value = name;
    document.getElementById('editBoardDescription').value = description || '';
    document.getElementById('editBoardForm').action = `/boards/${id}`;
    document.getElementById('editBoardModal').classList.remove('hidden');
}

function closeEditBoardModal() {
    document.getElementById('editBoardModal').classList.add('hidden');
}
</script>
@endsection