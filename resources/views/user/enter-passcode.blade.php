<x-layouts.app :title="'Enter passcode'">
    <x-slot name="headerActions">
        <a href="{{ route('landing') }}" class="text-sm underline">Back</a>
    </x-slot>
    <h1 class="text-2xl font-semibold mb-4">Enter your passcode</h1>
    <form method="post" action="{{ route('thread.auth') }}" class="space-y-4">
        @csrf
        <input name="passcode" placeholder="word-word-word-word" value="{{ old('passcode') }}" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
        <button class="px-4 py-2 rounded bg-indigo-600 text-white">Continue</button>
    </form>
</x-layouts.app>
