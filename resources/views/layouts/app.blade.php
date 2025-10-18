<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Messages App' }}</title>
    <script>
      (function(){
        const theme = localStorage.getItem('theme') || 'dark';
        if (theme === 'dark') document.documentElement.classList.add('dark');
      })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-gray-200 dark:border-gray-800">
            <div class="mx-auto max-w-4xl px-4 py-3 flex items-center justify-between">
                <a href="{{ url('/') }}" class="font-semibold">🗂️ Messages</a>
                <div class="flex items-center gap-3">
                   
                    {{ $headerActions ?? '' }}
                </div>
            </div>
        </header>
        <main class="flex-1 mx-auto w-full max-w-4xl px-4 py-6">
            @if (session('ok'))
                <div class="mb-4 rounded border border-green-600 text-green-700 dark:text-green-300 dark:border-green-700 px-3 py-2">{{ session('ok') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded border border-red-600 text-red-700 dark:text-red-300 dark:border-red-700 px-3 py-2">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $slot }}
        </main>
        <footer class="py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} — Demo app
        </footer>
    </div>
    <script>
      document.getElementById('themeToggle')?.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    </script>
</body>
</html>
