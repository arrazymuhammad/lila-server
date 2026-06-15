<!doctype html>
<html>

<head>
    <meta charset="utf-8">

    <title>
        LILA WebGIS
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet/dist/leaflet.css"
    >

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <aside
        class="w-64 bg-slate-900 text-white"
    >
        <div
            class="p-6 text-2xl font-bold"
        >
            LILA
        </div>

        <nav class="px-4 space-y-2">

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-slate-800"
            >
                Dashboard
            </a>

            <a
                href="{{ url('activities') }}"
                class="block px-4 py-3 rounded hover:bg-slate-800"
            >
                Aktivitas
            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-slate-800"
            >
                Verifikasi
            </a>

        </nav>
    </aside>

    <main class="flex-1">

        <header
            class="bg-white border-b px-6 py-4"
        >
            <h1
                class="text-xl font-semibold"
            >
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
