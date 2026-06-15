<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        LILA WebGIS
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    @yield('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 @yield('body_class')" >
    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white">
            <div class="p-6 text-2xl font-bold flex items-center gap-3">
                <img src="{{ url('assets/img/logo_revert.png') }}" alt="LILA" class="w-8 h-8">

                <span>LILA</span>
            </div>
            <nav class="px-4 space-y-2">
                <a href="#" class="block px-4 py-3 rounded hover:bg-slate-800">
                    Dashboard
                </a>
                <a href="{{ url('activities') }}" class="block px-4 py-3 rounded hover:bg-slate-800">
                    Aktivitas
                </a>
                <a href="#" class="block px-4 py-3 rounded hover:bg-slate-800">
                    Verifikasi
                </a>
            </nav>
        </aside>
        <main class="flex-1">
            <header class="bg-white border-b px-6 py-4">
                <h1 class="text-xl font-semibold flex items-center gap-4">
                    <img src="{{ url('assets/img/logo.png') }}" alt="" class="w-6 h-6">
                    LILA WebGIS
                </h1>
            </header>
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

</body>

</html>
