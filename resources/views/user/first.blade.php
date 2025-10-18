<x-layouts.app :title="'Submit'">
    <x-slot name="headerActions">
        <a href="{{ route('landing') }}" class="text-sm underline">Back</a>
    </x-slot>
    <h1 class="text-2xl font-semibold mb-4">Submit a message</h1>
    <form method="post" action="{{ route('first.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Subject</label>
            <input name="subject" value="{{ old('subject') }}" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Message</label>
            <textarea name="message" rows="6" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2" required>{{ old('message') }}</textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">File (optional)</label>
            <input type="file" name="file" class="block">
        </div>
        <button class="px-4 py-2 rounded bg-indigo-600 text-white">Submit</button>
    </form>
</x-layouts.app>
