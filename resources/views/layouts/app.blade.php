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
        <aside class="w-64 bg-slate-900 text-white flex flex-col h-screen sticky top-0">
            <div class="p-6 text-2xl font-bold flex items-center gap-3">
                <img src="{{ url('assets/img/logo_revert.png') }}" alt="LILA" class="w-8 h-8">

                <span>LILA</span>
            </div>
            <nav class="px-4 space-y-2">
                <a href="{{ url('dashboard') }}" class="block px-4 py-3 rounded {{ request()->is('dashboard') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                    Dashboard
                </a>
                <div class="pt-2">
                    <div class="px-4 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Perjalanan</div>
                    <a href="{{ url('activities') }}" class="block px-4 py-3 rounded {{ request()->is('activities*') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                        Daftar Perjalanan
                    </a>
                </div>
                <div class="pt-2">
                    <div class="px-4 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Temuan</div>
                    <a href="{{ url('findings') }}" class="block px-4 py-3 rounded {{ request()->is('findings*') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                        Daftar Temuan Pengamatan
                    </a>
                </div>
                <div class="pt-2">
                    <div class="px-4 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Peta</div>
                    <a href="{{ url('map') }}" class="block px-4 py-3 rounded {{ request()->is('map') || request()->is('maps') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                        Semua Rute
                    </a>
                </div>
                <div class="mt-6 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Verifikasi Data</div>
                <a href="{{ url('verifications') }}" class="block px-4 py-3 rounded {{ request()->is('verifications') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                    Antrian Perjalanan
                </a>
                <a href="{{ url('verifications/findings') }}" class="block px-4 py-3 rounded {{ request()->is('verifications/findings*') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                    Antrian Temuan
                </a>
                <div class="mt-6 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Master Data</div>
                <a href="{{ url('categories') }}" class="block px-4 py-3 rounded {{ request()->is('categories*') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                    Kategori Temuan
                </a>
                <a href="{{ url('mobile-users') }}" class="block px-4 py-3 rounded {{ request()->is('mobile-users*') ? 'bg-slate-800 font-semibold' : 'hover:bg-slate-800' }}">
                    Pengguna Mobile
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="mt-auto p-4 bg-slate-950 border-t border-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white shadow-inner">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <div class="text-sm font-semibold truncate text-slate-200">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-red-600/90 hover:text-white rounded-lg text-sm font-medium text-slate-300 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
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
