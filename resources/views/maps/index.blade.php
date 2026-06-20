@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('body_class', 'h-screen overflow-hidden')

@section('content')
    <div class="flex h-[calc(100vh-100px)] flex-col gap-3" x-data="allRoutesMap()" x-init="initMap()">
        <header class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-wide text-blue-700">Peta</div>
                    <h1 class="mt-1 text-3xl font-bold text-gray-950">Semua Rute</h1>
                    <p class="mt-1 text-gray-500">Seluruh rute perjalanan pada bulan dan tahun terpilih.</p>
                </div>

                <form method="GET" action="{{ url('map') }}" class="flex flex-wrap items-end gap-3">
                    <label>
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Bulan</span>
                        <select name="month"
                            class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @foreach (range(1, 12) as $monthOption)
                                <option value="{{ $monthOption }}" @selected($month === $monthOption)>
                                    {{ \Carbon\Carbon::create(null, $monthOption)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tahun</span>
                        <select name="year"
                            class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit"
                        class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Terapkan
                    </button>
                </form>
            </div>
        </header>

        <main class="grid min-h-0 flex-1 grid-cols-1 gap-3 xl:grid-cols-[1fr_380px]">
            <section class="relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div id="all-routes-map" class="h-full w-full"></div>
                <div x-show="notice"
                    class="absolute inset-x-4 bottom-4 z-[1000] rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm"
                    x-text="notice"></div>
            </section>

            <aside class="min-h-0 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="grid grid-cols-2 gap-3 border-b border-gray-100 p-4">
                    <div class="rounded-lg bg-gray-50 p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Perjalanan</div>
                        <div class="mt-1 text-xl font-bold text-gray-950">{{ number_format($summary['total_routes']) }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jarak</div>
                        <div class="mt-1 text-xl font-bold text-gray-950">{{ number_format($summary['total_distance'], 2) }} km</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Temuan</div>
                        <div class="mt-1 text-xl font-bold text-gray-950">{{ number_format($summary['total_findings']) }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Track Point</div>
                        <div class="mt-1 text-xl font-bold text-gray-950">{{ number_format($summary['total_track_points']) }}</div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($sessions as $session)
                        @php $route = $routes->firstWhere('id', $session->id); @endphp
                        <a href="{{ url('activities', $session) }}" class="block p-4 transition hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-3 w-3 rounded-full" style="background: {{ $route['color'] }}"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-bold text-gray-950">{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</div>
                                    <div class="mt-1 text-sm text-gray-500">{{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}</div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                                        <span class="rounded bg-gray-100 px-2 py-1">{{ number_format($session->distance, 2) }} km</span>
                                        <span class="rounded bg-gray-100 px-2 py-1">{{ $session->events_count }} temuan</span>
                                        <span class="rounded bg-gray-100 px-2 py-1">{{ $session->track_points_count }} point</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">Tidak ada perjalanan pada periode ini.</div>
                    @endforelse
                </div>
            </aside>
        </main>
    </div>

    <script>
        function allRoutesMap() {
            return {
                map: null,
                notice: '',
                routes: @json($routes),

                initMap() {
                    this.map = L.map('all-routes-map');

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    this.renderRoutes();
                },

                renderRoutes() {
                    const bounds = [];

                    this.routes.forEach((route) => {
                        if (route.coordinates.length >= 2) {
                            const polyline = L.polyline(route.coordinates, {
                                color: route.color,
                                weight: 5,
                                opacity: 0.85
                            }).addTo(this.map);

                            polyline.bindPopup(`
                                <strong>${route.title}</strong><br>
                                ${route.start_time || '-'}<br>
                                ${Number(route.distance).toFixed(2)} km<br>
                                <a href="${route.url}">Detail Perjalanan</a>
                            `);

                            route.coordinates.forEach((coordinate) => bounds.push(coordinate));
                        }

                        route.findings.forEach((finding) => {
                            L.circleMarker([finding.latitude, finding.longitude], {
                                radius: 6,
                                color: route.color,
                                fillColor: route.color,
                                fillOpacity: 0.9
                            }).addTo(this.map).bindPopup(`
                                <strong>${finding.title}</strong><br>
                                <a href="${finding.url}">Detail Temuan</a>
                            `);

                            bounds.push([finding.latitude, finding.longitude]);
                        });
                    });

                    if (bounds.length) {
                        this.map.fitBounds(bounds, {
                            padding: [40, 40],
                            maxZoom: 15
                        });
                        return;
                    }

                    this.map.setView([-2.5489, 118.0149], 5);
                    this.notice = 'Belum ada rute atau koordinat temuan pada periode ini.';
                },
            };
        }
    </script>
@endsection
