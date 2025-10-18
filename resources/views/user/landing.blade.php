<x-layouts.app :title="'Home'">
    <x-slot name="headerActions"></x-slot>
    @include('components.logo')
    <div class="grid gap-4">
        <a href="{{ route('first.create') }}" class="px-6 py-4 rounded-xl bg-indigo-600 text-white text-center hover:opacity-90">First time submitting</a>
        <a href="{{ route('thread.enter') }}" class="px-6 py-4 rounded-xl bg-gray-700 text-white text-center hover:opacity-90">I have submitted already</a>
    </div>
</x-layouts.app>
