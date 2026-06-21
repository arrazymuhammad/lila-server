@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <style>
        .leaflet-popup-content-wrapper { padding: 0; overflow: hidden; border-radius: 0.5rem; }
        .leaflet-popup-content { margin: 0; width: 256px !important; }
    </style>
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

                <div class="flex items-center gap-6">
                    <label class="flex items-center cursor-pointer" title="Tampilkan heatmap temuan">
                        <div class="relative">
                            <input type="checkbox" x-model="showFindingHeatmap" @change="toggleFindingHeatmap" class="sr-only">
                            <div class="block w-10 h-6 rounded-full transition" :class="showFindingHeatmap ? 'bg-purple-600' : 'bg-gray-300'"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition" :class="showFindingHeatmap ? 'transform translate-x-4' : ''"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700">
                            Heatmap Temuan
                        </div>
                    </label>

                    <label class="flex items-center cursor-pointer" title="Tampilkan heatmap perjalanan">
                        <div class="relative">
                            <input type="checkbox" x-model="showHeatmap" @change="toggleHeatmap" class="sr-only">
                            <div class="block w-10 h-6 rounded-full transition" :class="showHeatmap ? 'bg-rose-600' : 'bg-gray-300'"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition" :class="showHeatmap ? 'transform translate-x-4' : ''"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700">
                            Heatmap Rute
                        </div>
                    </label>

                    <label class="flex items-center cursor-pointer" title="Tampilkan temuan yang belum diverifikasi">
                        <div class="relative">
                            <input type="checkbox" x-model="showAllFindings" @change="toggleFindings" class="sr-only">
                            <div class="block w-10 h-6 rounded-full transition" :class="showAllFindings ? 'bg-blue-600' : 'bg-gray-300'"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition" :class="showAllFindings ? 'transform translate-x-4' : ''"></div>
                        </div>
                        <div class="ml-3 text-sm font-medium text-gray-700">
                            Semua Temuan
                        </div>
                    </label>

                    <form method="GET" action="{{ url('map') }}" class="flex flex-wrap items-end gap-3">
                    <label x-show="showFindingHeatmap" style="display: none;">
                        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori Temuan</span>
                        <select x-model="selectedCategory" @change="refreshMap"
                            class="h-10 rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-purple-50">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </label>
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
        window.changePhoto = function(event, direction) {
            const popup = event.target.closest('.leaflet-popup-content');
            if (!popup) return;

            const container = popup.querySelector('[data-photos]');
            if (!container) return;

            const photos = JSON.parse(container.dataset.photos);
            let currentIndex = parseInt(container.dataset.index);

            currentIndex += direction;
            if (currentIndex < 0) currentIndex = photos.length - 1;
            if (currentIndex >= photos.length) currentIndex = 0;

            container.dataset.index = currentIndex;

            const img = container.querySelector('img');
            const counter = container.querySelector('.photo-counter');

            img.src = photos[currentIndex];
            counter.textContent = `${currentIndex + 1}/${photos.length}`;
        };

        function allRoutesMap() {
            return {
                map: null,
                notice: '',
                routes: @json($routes),
                showAllFindings: localStorage.getItem('lila_show_all_findings') === 'true',
                showHeatmap: localStorage.getItem('lila_show_heatmap') === 'true',
                showFindingHeatmap: localStorage.getItem('lila_show_finding_heatmap') === 'true',
                selectedCategory: '',
                mapLayers: [],

                initMap() {
                    this.map = L.map('all-routes-map');

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    this.renderRoutes();
                },

                toggleFindings() {
                    localStorage.setItem('lila_show_all_findings', this.showAllFindings);
                    this.refreshMap();
                },

                toggleHeatmap() {
                    if (this.showHeatmap) {
                        this.showFindingHeatmap = false;
                        localStorage.setItem('lila_show_finding_heatmap', false);
                    }
                    localStorage.setItem('lila_show_heatmap', this.showHeatmap);
                    this.refreshMap();
                },

                toggleFindingHeatmap() {
                    if (this.showFindingHeatmap) {
                        this.showHeatmap = false;
                        localStorage.setItem('lila_show_heatmap', false);
                    }
                    localStorage.setItem('lila_show_finding_heatmap', this.showFindingHeatmap);
                    this.refreshMap();
                },

                refreshMap() {
                    this.mapLayers.forEach(layer => this.map.removeLayer(layer));
                    this.mapLayers = [];
                    this.renderRoutes();
                },

                renderRoutes() {
                    let bounds = [];
                    let heatPoints = [];
                    let findingHeatPoints = [];

                    this.routes.forEach((route) => {
                        if (route.coordinates.length >= 2) {
                            if (this.showHeatmap) {
                                route.coordinates.forEach(coord => heatPoints.push([...coord, 1]));
                                route.coordinates.forEach(coordinate => bounds.push(coordinate));
                            } else if (!this.showFindingHeatmap) {
                                const polyline = L.polyline(route.coordinates, {
                                    color: route.color,
                                    weight: 5,
                                    opacity: 0.85
                                }).addTo(this.map);

                                polyline.bindPopup(`
                                    <div class="p-4 w-64">
                                        <div class="font-bold text-gray-900 mb-1">${route.title}</div>
                                        <div class="text-xs text-gray-500 mb-3">${route.start_time || '-'}</div>
                                        <div class="flex gap-2 mb-3">
                                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">${Number(route.distance).toFixed(2)} km</span>
                                        </div>
                                        <a href="${route.url}" class="block w-full text-center bg-blue-50 text-blue-600 hover:bg-blue-100 font-medium text-xs py-1.5 rounded transition">Detail Perjalanan</a>
                                    </div>
                                `, { minWidth: 256, maxWidth: 256 });

                                this.mapLayers.push(polyline);
                                route.coordinates.forEach((coordinate) => bounds.push(coordinate));
                            }
                        }

                        route.findings.forEach((finding) => {
                            if (!this.showAllFindings && finding.status !== 'verified') {
                                return;
                            }

                            let renderMarker = true;

                            if (this.showFindingHeatmap) {
                                if (this.selectedCategory === '' || finding.operator_category === this.selectedCategory) {
                                    findingHeatPoints.push([finding.latitude, finding.longitude, 1]);
                                    bounds.push([finding.latitude, finding.longitude]);
                                } else {
                                    renderMarker = false; // Sembunyikan marker jika tidak cocok dengan filter
                                }
                            }

                            if (!renderMarker) return;

                            const isSubmitted = finding.status === 'submitted';
                            const markerColor = isSubmitted ? '#9ca3af' : route.color;

                            const safePhotos = (finding.photos || []).map(p => p.replace(/"/g, '"'));
                            const photosJson = JSON.stringify(safePhotos).replace(/"/g, '"');

                            let popupContent = `<div class="w-full flex flex-col">`;

                            if (finding.photos && finding.photos.length > 0) {
                                popupContent += `
                                <div class="relative w-full h-32 bg-gray-100" data-photos='${photosJson}' data-index="0">
                                    <img src="${safePhotos[0]}" class="object-cover w-full h-full" alt="Foto Temuan" />
                                    ${finding.photos.length > 1 ? `
                                        <span class="photo-counter absolute top-2 right-2 bg-black bg-opacity-60 text-white text-[10px] px-1.5 py-0.5 rounded backdrop-blur-sm">1/${finding.photos.length}</span>
                                        <button onclick="window.changePhoto(event, -1)" class="absolute left-1 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full shadow-sm text-gray-800 transition">&lsaquo;</button>
                                        <button onclick="window.changePhoto(event, 1)" class="absolute right-1 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full shadow-sm text-gray-800 transition">&rsaquo;</button>
                                    ` : ''}
                                </div>`;
                            } else {
                                popupContent += `
                                <div class="w-full h-16 bg-gray-50 flex items-center justify-center text-gray-400 text-xs border-b border-gray-100">
                                    Tidak ada foto
                                </div>`;
                            }

                            popupContent += `
                                <div class="p-3">
                                    ${finding.operator_category ? `<div class="inline-block px-1.5 py-0.5 mb-2 bg-blue-50 border border-blue-100 text-blue-700 text-[10px] font-semibold rounded">${finding.operator_category}</div>` : ''}
                                    <div class="font-bold text-gray-900 mb-1 leading-tight text-sm">${finding.title}</div>
                                    <div class="text-[10px] text-gray-500 mb-2">${finding.timestamp || '-'}</div>
                                    ${finding.description ? `<p class="text-xs text-gray-600 mb-3 line-clamp-2 leading-relaxed">${finding.description}</p>` : ''}
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded uppercase font-bold tracking-wide ${isSubmitted ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'}">${finding.status || 'unknown'}</span>
                                        ${!isSubmitted ? `<a href="${finding.url}" class="text-xs text-blue-600 font-medium hover:text-blue-700">Detail &rarr;</a>` : ''}
                                    </div>
                                </div>
                            </div>`;

                            const marker = L.circleMarker([finding.latitude, finding.longitude], {
                                radius: 6,
                                color: markerColor,
                                fillColor: markerColor,
                                fillOpacity: 0.9
                            }).addTo(this.map).bindPopup(popupContent, { minWidth: 256, maxWidth: 256 });

                            this.mapLayers.push(marker);
                            bounds.push([finding.latitude, finding.longitude]);
                        });
                    });

                    if (this.showHeatmap && heatPoints.length > 0) {
                        const heatLayer = L.heatLayer(heatPoints, {
                            radius: 20,
                            blur: 15,
                            maxZoom: 15
                        }).addTo(this.map);
                        this.mapLayers.push(heatLayer);
                    }

                    if (this.showFindingHeatmap && findingHeatPoints.length > 0) {
                        // Gunakan warna gradien berbeda untuk membedakan dari heatmap rute (opsional, leaflet-heat default = blue-red)
                        const heatFindingLayer = L.heatLayer(findingHeatPoints, {
                            radius: 25,
                            blur: 20,
                            maxZoom: 15,
                            gradient: {0.4: 'purple', 0.6: 'fuchsia', 0.8: 'red', 1: 'yellow'}
                        }).addTo(this.map);
                        this.mapLayers.push(heatFindingLayer);
                    }

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
