<x-layouts.app :title="'All Messages'">
    <x-slot name="headerActions">
        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button class="text-sm underline">Logout</button>
        </form>
    </x-slot>

    <h1 class="text-2xl font-semibold mb-4">All conversations</h1>
    <form method="get" class="flex gap-2 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search subject..." class="flex-1 rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
        <select name="status" class="rounded border border-gray-300 dark:border-gray-700 bg-white/70 dark:bg-gray-800 px-3 py-2">
            <option value="">All statuses</option>
            <option value="new_from_user" @selected($status==='new_from_user')>New message from User</option>
            <option value="add_more" @selected($status==='add_more')>Add more</option>
            <option value="closed" @selected($status==='closed')>Closed</option>
        </select>
        <button class="px-3 py-2 rounded bg-gray-800 text-white">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="text-left px-3 py-2">ID</th>
                    <th class="text-left px-3 py-2">Subject</th>
                    <th class="text-left px-3 py-2">Created</th>
                    <th class="text-left px-3 py-2">Status</th>
                    <th class="text-left px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($threads as $t)
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-3 py-2">{{ $t->id }}</td>
                        <td class="px-3 py-2">{{ $t->first_subject ?? '(n/a)' }}</td>
                        <td class="px-3 py-2">{{ $t->created_at }}</td>
                        <td class="px-3 py-2">{{ $t->status }}</td>
                        <td class="px-3 py-2"><a href="{{ route('admin.messages.show', $t) }}" class="underline">Open</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $threads->withQueryString()->links() }}
    </div>
</x-layouts.app>
