<x-layouts.app :title="'Your messages'">
    <x-slot name="headerActions">
        <form method="post" action="{{ route('thread.logout') }}">
            @csrf
            <button class="text-sm underline">Logout</button>
        </form>
    </x-slot>

    <h1 class="text-2xl font-semibold mb-4">Messages</h1>

    <div class="space-y-4 mb-8">
        @foreach ($messages as $m)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 {{ $m->direction === 'admin' ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800' }}">
                <div class="flex items-center justify-between">
                    <div class="text-sm uppercase tracking-wide opacity-70">{{ $m->direction }}</div>
                    <div class="text-xs opacity-60">{{ $m->created_at }}</div>
                </div>
                <div class="mt-2 font-semibold">{{ $m->subject }}</div>
                <div class="prose prose-invert max-w-none mt-2">{!! $m->body !!}</div>
                @if ($m->file_path)
                    <div class="mt-2">
                        <a href="{{ route('file.download', $m->id) }}" class="text-sm underline">Download: {{ $m->file_original }} ({{ number_format($m->file_size/1024,0) }} KB)</a>
                    </div>
                @endif
                @if ($m->direction === 'user')
                    <form method="post" action="{{ route('thread.message.delete', $m->id) }}" class="mt-2" onsubmit="return confirm('Delete this message?');">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 underline">Delete</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <h2 class="text-xl font-semibold mb-2">Add a new message</h2>
    <form method="post" action="{{ route('thread.message.add') }}" enctype="multipart/form-data" class="space-y-4">
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
</x-layouts.app>
