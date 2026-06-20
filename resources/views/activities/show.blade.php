@extends('layouts.app')

@php
    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return "{$hours}j {$minutes}m";
    };
@endphp

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('body_class', 'h-screen overflow-hidden')

@section('content')
    <div class="flex h-[calc(100vh-100px)] flex-col gap-3" x-data="activityDetail()" x-init="initMap()">
        <header class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 p-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="min-w-0">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <a href="{{ url('activities') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Daftar Perjalanan</a>
                        <span class="text-sm text-gray-300">/</span>
                        <x-status-badge :status="$session->status" />
                    </div>
                    <h1 class="truncate text-2xl font-bold text-gray-950 xl:text-3xl">
                        {{ $session->title ?? 'Perjalanan Tanpa Nama' }}
                    </h1>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}
                        @if ($session->end_time)
                            - {{ $session->end_time->format('H:i') }}
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-5 xl:w-[640px]">
                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                        <div class="text-xs font-medium text-gray-500">Jarak</div>
                        <div class="mt-1 font-bold text-gray-950">{{ number_format($session->distance, 2) }} km</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                        <div class="text-xs font-medium text-gray-500">Durasi</div>
                        <div class="mt-1 font-bold text-gray-950">{{ $formatDuration($session->duration_seconds) }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                        <div class="text-xs font-medium text-gray-500">Temuan</div>
                        <div class="mt-1 font-bold text-gray-950">{{ $summary['events'] }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                        <div class="text-xs font-medium text-gray-500">Foto</div>
                        <div class="mt-1 font-bold text-gray-950">{{ $summary['photos'] }}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                        <div class="text-xs font-medium text-gray-500">Point</div>
                        <div class="mt-1 font-bold text-gray-950">{{ $summary['track_points'] }}</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex min-h-0 flex-1 gap-3">
            <section class="relative min-w-0 flex-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div id="map" class="h-full w-full"></div>

                <div class="absolute left-4 top-4 z-[1000] rounded-lg border border-gray-200 bg-white/95 p-3 shadow-sm backdrop-blur">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rute</div>
                    <div class="mt-1 text-sm font-bold text-gray-950">{{ number_format($session->distance, 2) }} km</div>
                </div>

                <button x-show="!sidebarVisible" @click="toggleSidebar()"
                    class="absolute right-4 top-4 z-[1000] h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white shadow-lg">
                    Temuan
                </button>

                <div x-show="mapNotice"
                    class="absolute inset-x-4 bottom-4 z-[1000] rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm"
                    x-text="mapNotice">
                </div>
            </section>

            <aside class="flex min-h-0 flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300"
                :class="sidebarVisible ? 'w-[440px]' : 'w-0 border-0'" x-show="sidebarVisible" x-transition>
                <div class="flex items-center justify-between border-b border-gray-100 p-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500" x-text="sidebarMode === 'list' ? 'Daftar Temuan Pengamatan' : 'Detail Temuan'"></div>
                        <h2 class="mt-1 text-lg font-bold text-gray-950" x-text="sidebarTitle()"></h2>
                    </div>
                    <button @click="toggleSidebar()" class="h-9 rounded-lg border border-gray-200 px-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Tutup
                    </button>
                </div>

                <template x-if="sidebarMode === 'list'">
                    <div class="flex min-h-0 flex-1 flex-col">
                        <div class="grid grid-cols-3 gap-3 border-b border-gray-100 p-4">
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <div class="text-xs text-gray-500">Temuan</div>
                                <div class="font-bold text-gray-950" x-text="session.events.length"></div>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <div class="text-xs text-gray-500">Foto</div>
                                <div class="font-bold text-gray-950" x-text="session.photos.length"></div>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <div class="text-xs text-gray-500">Terpilih</div>
                                <div class="font-bold text-gray-950">{{ $summary['selected_photos'] }}</div>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto">
                            <template x-if="session.events.length === 0">
                                <div class="p-6 text-center text-sm text-gray-500">
                                    Belum ada temuan pengamatan pada perjalanan ini.
                                </div>
                            </template>

                            <template x-for="(event, index) in session.events" :key="event.id">
                                <button @click="openEvent(event, index)"
                                    class="w-full border-b border-gray-100 px-4 py-4 text-left transition hover:bg-gray-50">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-600 text-sm font-bold text-white"
                                            x-text="index + 1"></div>
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate font-semibold text-gray-950" x-text="titleOrDefault(event, index)"></div>
                                            <div class="mt-1 text-sm text-gray-500" x-text="formatDateTime(event.timestamp)"></div>
                                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                                                <span class="rounded bg-gray-100 px-2 py-1">
                                                    <span x-text="event.photos?.length ?? 0"></span> foto
                                                </span>
                                                <span class="rounded bg-gray-100 px-2 py-1" x-text="coordinateText(event)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="sidebarMode === 'detail'">
                    <div class="min-h-0 flex-1 overflow-y-auto">
                        <div class="border-b border-gray-100 p-4">
                            <button @click="sidebarMode = 'list'; selectedEvent = null"
                                class="mb-4 rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Kembali
                            </button>

                            <template x-if="selectedEvent?.photos?.length">
                                <div>
                                    <div class="relative overflow-hidden rounded-lg bg-gray-100">
                                        <img :src="photoUrl(selectedEvent.photos[selectedPhotoIndex])"
                                            class="h-64 w-full object-cover" alt="">
                                        <div class="absolute bottom-2 left-2 rounded bg-black/70 px-2 py-1 text-xs font-semibold text-white">
                                            <span x-text="selectedPhotoIndex + 1"></span>/<span x-text="selectedEvent.photos.length"></span>
                                        </div>
                                        <button x-show="selectedPhotoIndex > 0" @click="selectedPhotoIndex--"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 rounded-lg bg-white px-3 py-2 text-lg font-bold shadow">
                                            &lt;
                                        </button>
                                        <button x-show="selectedPhotoIndex < selectedEvent.photos.length - 1" @click="selectedPhotoIndex++"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-white px-3 py-2 text-lg font-bold shadow">
                                            &gt;
                                        </button>
                                    </div>

                                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                        <template x-for="(photo, index) in selectedEvent.photos" :key="photo.id">
                                            <button @click="selectedPhotoIndex = index"
                                                class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border bg-gray-100"
                                                :class="selectedPhotoIndex === index ? 'border-blue-600 ring-2 ring-blue-100' : 'border-gray-200'">
                                                <img :src="photoUrl(photo)" class="h-full w-full object-cover" alt="">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="selectedEvent && !selectedEvent.photos?.length">
                                <div class="rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">
                                    Temuan ini belum memiliki foto.
                                </div>
                            </template>
                        </div>

                        <div class="space-y-5 p-4" x-show="selectedEvent">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Judul Temuan</div>
                                <div class="mt-1 text-xl font-bold text-gray-950" x-text="titleOrDefault(selectedEvent, selectedEventIndex)"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <div class="text-xs font-medium text-gray-500">Waktu</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900" x-text="formatDateTime(selectedEvent?.timestamp)"></div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <div class="text-xs font-medium text-gray-500">Koordinat</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900" x-text="coordinateText(selectedEvent)"></div>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Deskripsi</div>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700"
                                    x-text="descriptionOrDefault(selectedEvent)"></p>
                            </div>

                            <a :href="'/findings/' + selectedEvent.id"
                                class="inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Buka Detail Temuan
                            </a>
                        </div>
                    </div>
                </template>
            </aside>
        </main>
    </div>

    <script>
        function activityDetail() {
            return {
                map: null,
                mapNotice: '',
                sidebarVisible: true,
                sidebarMode: 'list',
                selectedEvent: null,
                selectedEventIndex: 0,
                selectedPhotoIndex: 0,
                markers: [],
                session: @json($session),

                initMap() {
                    this.map = L.map('map', {
                        zoomControl: false
                    });

                    L.control.zoom({
                        position: 'bottomright'
                    }).addTo(this.map);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    this.renderTrack();
                    this.renderEvents();
                    this.fitMap();
                },

                toggleSidebar() {
                    this.sidebarVisible = !this.sidebarVisible;
                    this.$nextTick(() => {
                        setTimeout(() => this.map.invalidateSize(), 300);
                    });
                },

                sidebarTitle() {
                    if (this.sidebarMode === 'detail' && this.selectedEvent) {
                        return this.titleOrDefault(this.selectedEvent, this.selectedEventIndex);
                    }

                    return `${this.session.events.length} temuan tercatat`;
                },

                renderTrack() {
                    const coordinates = this.session.track_points
                        .filter(point => point.latitude && point.longitude)
                        .map(point => [point.latitude, point.longitude]);

                    if (coordinates.length < 2) {
                        this.mapNotice = 'Track point belum cukup untuk menggambar rute.';
                        return;
                    }

                    L.polyline(coordinates, {
                        color: '#2563eb',
                        weight: 5,
                        opacity: 0.85
                    }).addTo(this.map);
                },

                renderEvents() {
                    this.session.events.forEach((event, index) => {
                        if (!event.latitude || !event.longitude) {
                            return;
                        }

                        const marker = L.marker([event.latitude, event.longitude], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div style="background:#e11d48;color:white;width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;border:2px solid white;box-shadow:0 8px 20px rgba(15,23,42,.25);">${index + 1}</div>`,
                                iconSize: [34, 34],
                                iconAnchor: [17, 17],
                            })
                        }).addTo(this.map);

                        marker.on('click', () => this.openEvent(event, index));
                        this.markers.push(marker);
                    });
                },

                fitMap() {
                    const bounds = [];

                    this.session.track_points.forEach(point => {
                        if (point.latitude && point.longitude) {
                            bounds.push([point.latitude, point.longitude]);
                        }
                    });

                    this.session.events.forEach(event => {
                        if (event.latitude && event.longitude) {
                            bounds.push([event.latitude, event.longitude]);
                        }
                    });

                    if (bounds.length) {
                        this.map.fitBounds(bounds, {
                            padding: [40, 40],
                            maxZoom: 17
                        });
                        return;
                    }

                    this.map.setView([-2.5489, 118.0149], 5);
                    this.mapNotice = 'Belum ada koordinat untuk ditampilkan pada peta.';
                },

                openEvent(event, index) {
                    this.selectedEvent = event;
                    this.selectedEventIndex = index;
                    this.selectedPhotoIndex = 0;
                    this.sidebarMode = 'detail';
                    this.sidebarVisible = true;

                    if (event.latitude && event.longitude) {
                        this.map.setView([event.latitude, event.longitude], Math.max(this.map.getZoom(), 16), {
                            animate: true
                        });
                    }
                },

                photoUrl(photo) {
                    return '/' + (photo.thumbnail_path || photo.file_path || '');
                },

                titleOrDefault(event, index) {
                    return event?.title?.trim() ? event.title : `Temuan ${index + 1}`;
                },

                descriptionOrDefault(event) {
                    return event?.description?.trim() ? event.description : 'Belum ada deskripsi.';
                },

                coordinateText(item) {
                    if (!item?.latitude || !item?.longitude) {
                        return 'Koordinat kosong';
                    }

                    return `${Number(item.latitude).toFixed(5)}, ${Number(item.longitude).toFixed(5)}`;
                },

                formatDateTime(timestamp) {
                    if (!timestamp) {
                        return '-';
                    }

                    const date = new Date(timestamp);

                    return date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },
            }
        }
    </script>
@endsection
