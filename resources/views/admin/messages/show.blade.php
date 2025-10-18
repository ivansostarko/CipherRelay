<x-layouts.app :title="'Thread #'.$thread->id">
    <x-slot name="headerActions">
        <a href="{{ route('admin.messages.index') }}" class="text-sm underline">Back</a>
        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button class="text-sm underline">Logout</button>
        </form>
    </x-slot>

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Conversation #{{ $thread->id }}</h1>
        <form method="post" action="{{ route('admin.messages.status', $thread) }}" class="flex gap-2 items-center">
            @csrf
            <select name="status" class="rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
                <option value="new_from_user" @selected($thread->status==='new_from_user')>New message from User</option>
                <option value="add_more" @selected($thread->status==='add_more')>Add more</option>
                <option value="closed" @selected($thread->status==='closed')>Closed</option>
            </select>
            <button class="px-3 py-2 rounded bg-gray-800 text-white">Update</button>
        </form>
    </div>

    <div class="space-y-4 mb-8">
        @foreach ($viewMsgs as $m)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 {{ $m->direction === 'user' ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800' }}">
                <div class="flex items-center justify-between">
                    <div class="text-sm uppercase tracking-wide opacity-70">{{ $m->direction }}</div>
                    <div class="text-xs opacity-60">{{ $m->created_at }}</div>
                </div>
                <div class="mt-2 font-semibold">{{ $m->subject }}</div>
                <div class="prose prose-invert max-w-none mt-2">{!! $m->body !!}</div>
                @if ($m->file_path)
                    <div class="mt-2">
                        <a href="{{ route('admin.file.download', [$thread, $m->id]) }}" class="text-sm underline">Download: {{ $m->file_original }} ({{ number_format($m->file_size/1024,0) }} KB)</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <h2 class="text-xl font-semibold mb-2">Reply</h2>
    <form method="post" action="{{ route('admin.messages.add', $thread) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Subject</label>
            <input name="subject" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Message</label>
            <textarea name="message" rows="5" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2" required></textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">File (optional)</label>
            <input type="file" name="file">
        </div>
        <button class="px-4 py-2 rounded bg-indigo-600 text-white">Send</button>
    </form>

    <h2 class="text-xl font-semibold mt-8 mb-2">Notes</h2>
    <form method="post" action="{{ route('admin.messages.note', $thread) }}" class="mb-4">
        @csrf
        <textarea name="note" rows="3" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2" required></textarea>
        <button class="mt-2 px-3 py-2 rounded bg-gray-800 text-white">Add note</button>
    </form>
    <ul class="space-y-2">
        @foreach ($notes as $n)
            <li class="rounded border border-gray-200 dark:border-gray-700 p-3 text-sm">
                <div class="opacity-60">{{ $n->created_at }}</div>
                <div>{{ $n->note }}</div>
            </li>
        @endforeach
    </ul>
</x-layouts.app>
