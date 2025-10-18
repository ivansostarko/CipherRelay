<x-layouts.app :title="'Your Passcode'">
    <x-slot name="headerActions">
        <form method="post" action="{{ route('thread.logout') }}">
            @csrf
            <button class="text-sm underline">Logout</button>
        </form>
    </x-slot>
    <h1 class="text-2xl font-semibold mb-2">Save your passcode</h1>
    <p class="mb-4 text-gray-600 dark:text-gray-400">You'll use this code to access and continue the conversation.</p>
    <div class="p-4 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-2xl font-mono tracking-wide select-all text-center">
        {{ $passcode }}
    </div>
    <div class="mt-6">
        <a href="{{ route('thread.show') }}" class="px-4 py-2 rounded bg-indigo-600 text-white">Continue</a>
    </div>
</x-layouts.app>
