<x-layouts.app :title="'Admin Login'">
    <h1 class="text-2xl font-semibold mb-4">Admin Login</h1>
    <form method="post" action="{{ route('admin.login.post') }}" class="space-y-4 max-w-sm">
        @csrf
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email','admin@example.com') }}" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Password</label>
            <input type="password" name="password" value="password" class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
        </div>
        <button class="px-4 py-2 rounded bg-indigo-600 text-white">Login</button>
    </form>
</x-layouts.app>
