@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-sm text-blue-500 hover:text-blue-700">
                Mark all as read
            </button>
        </form>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
        <a href="{{ route('boards.show', $notification->board_id) }}" 
           class="block p-4 hover:bg-gray-50 transition border-b border-gray-100 {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
           onclick="markAsRead({{ $notification->id }})">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                        {{ $notification->type === 'mention' ? 'bg-green-100' : ($notification->type === 'comment' ? 'bg-blue-100' : 'bg-purple-100') }}">
                        <span class="text-lg">{{ $notification->type === 'mention' ? '💬' : ($notification->type === 'comment' ? '📝' : '📌') }}</span>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $notification->title }}</p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $notification->message }}</p>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                        @if($notification->fromUser)
                        <p class="text-xs text-gray-400">From: {{ $notification->fromUser->name }}</p>
                        @endif
                    </div>
                </div>
                @if(!$notification->is_read)
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                @endif
            </div>
        </a>
        @empty
        <div class="p-8 text-center text-gray-400">
            <div class="text-5xl mb-3">🔔</div>
            <p>No notifications yet</p>
            <p class="text-sm mt-1">You'll be notified when someone mentions you or comments on your tasks</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
</script>
@endsection