@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-extrabold uppercase" style="color: #123b7a;">Workspace</p>
            <h1 class="mt-1 text-3xl font-extrabold" style="color: #071a3d;">Dashboard Kanban</h1>
            <p class="mt-1 text-sm" style="color: #64748b;">Pantau board, pekerjaan, dan kolaborasi tim dalam satu tempat.</p>
        </div>
        <button onclick="openCreateBoardModal()" class="btn-accent flex items-center justify-center gap-2">
            <span class="text-lg leading-none">+</span> New Board
        </button>
    </div>

    @if($ownedBoards->count() > 0)
    <div class="mb-10">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-extrabold" style="color: #071a3d;">My Boards</h2>
            <p class="hidden text-sm sm:block" style="color: #64748b;">Board yang kamu kelola langsung.</p>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($ownedBoards as $board)
            <div class="board-card group relative">
                <div class="board-card-accent"></div>
                <div class="p-5">
                    <div class="mb-2 flex items-start justify-between gap-3">
                        <a href="{{ route('boards.show', $board) }}" class="min-w-0 flex-1">
                            <h3 class="truncate text-lg font-extrabold" style="color: #071a3d;">{{ $board->name }}</h3>
                        </a>
                        <button onclick="openEditBoardModal({{ $board->id }}, '{{ addslashes($board->name) }}', '{{ addslashes($board->description) }}')"
                                class="rounded-lg p-1 text-gray-500 opacity-0 transition hover:bg-blue-50 hover:text-blue-700 group-hover:opacity-100"
                                title="Edit board">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('boards.show', $board) }}">
                        <p class="line-clamp-2 text-sm" style="color: #64748b;">{{ $board->description ?? 'No description' }}</p>
                        <div class="mt-5 flex items-center justify-between gap-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold" style="background: #eef5ff; color: #123b7a;">{{ $board->lists->count() }} lists</span>
                            @if($board->sharedUsers->count() > 0)
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold" style="background-color: #fff1df; color: #c2410c;">
                                Shared with {{ $board->sharedUsers->count() }}
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
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-extrabold" style="color: #071a3d;">Shared with Me</h2>
            <p class="hidden text-sm sm:block" style="color: #64748b;">Board yang dibagikan oleh tim.</p>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($sharedBoards as $board)
            <div class="board-card">
                <div class="board-card-primary"></div>
                <a href="{{ route('boards.show', $board) }}" class="block p-5">
                    <h3 class="mb-2 truncate text-lg font-extrabold" style="color: #071a3d;">{{ $board->name }}</h3>
                    <p class="line-clamp-2 text-sm" style="color: #64748b;">{{ $board->description ?? 'No description' }}</p>
                    <div class="mt-5 flex items-center justify-between gap-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold" style="background: #eef5ff; color: #123b7a;">{{ $board->lists->count() }} lists</span>
                        <span class="truncate rounded-full px-2.5 py-1 text-xs font-bold" style="background-color: #f8fafc; color: #52657d;">
                            Owner: {{ $board->owner->name }}
                        </span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($ownedBoards->count() === 0 && $sharedBoards->count() === 0)
    <div class="mx-auto max-w-xl rounded-lg border bg-white px-6 py-12 text-center shadow-sm" style="border-color: #d8e2ee;">
        <div class="mx-auto mb-4 grid h-12 w-12 place-items-center rounded-lg text-white font-extrabold" style="background: linear-gradient(135deg, #071a3d, #123b7a);">K</div>
        <p class="mb-4 text-sm" style="color: #64748b;">Kamu belum punya board. Mulai dari satu board kecil untuk merapikan prioritas.</p>
        <button onclick="openCreateBoardModal()" class="btn-accent">
            Create your first board
        </button>
    </div>
    @endif
</div>

<div id="createBoardModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-slate-900 bg-opacity-50">
    <div class="relative top-20 mx-auto w-96 rounded-lg border bg-white p-6 shadow-xl" style="border-color: #d8e2ee;">
        <h3 class="mb-4 text-xl font-extrabold" style="color: #071a3d;">Create New Board</h3>
        <form action="{{ route('boards.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Board Name" required
                   class="mb-3 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <textarea name="description" placeholder="Description (optional)" rows="3"
                      class="mb-4 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCreateBoardModal()" class="rounded-lg bg-gray-100 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-200">Cancel</button>
                <button type="submit" class="btn-accent">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editBoardModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-slate-900 bg-opacity-50">
    <div class="relative top-20 mx-auto w-96 rounded-lg border bg-white p-6 shadow-xl" style="border-color: #d8e2ee;">
        <h3 class="mb-4 text-xl font-extrabold" style="color: #071a3d;">Edit Board</h3>
        <form id="editBoardForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editBoardId" name="board_id">
            <input type="text" id="editBoardName" name="name" placeholder="Board Name" required
                   class="mb-3 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <textarea id="editBoardDescription" name="description" placeholder="Description (optional)" rows="3"
                      class="mb-4 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditBoardModal()" class="rounded-lg bg-gray-100 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-200">Cancel</button>
                <button type="submit" class="btn-accent">Save Changes</button>
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
