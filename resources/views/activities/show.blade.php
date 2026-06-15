@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection
@section('body_class', 'h-screen overflow-hidden')
@section('content')
    <div class="h-[calc(100vh-100px)] flex flex-col" x-data="data()" x-init="initMap()">
        <header class="mb-2">
            <div class="bg-white rounded-xl border px-6 py-3">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-3xl font-bold">
                                {{ $session->title ?? 'Aktivitas Tanpa Nama' }}
                            </h1>
                        </div>

                        <div class="mt-2 flex items-center gap-3">
                            <div class="text-gray-500">
                                {{ $session->start_time->format('d M Y, H:i') }}
                                -
                                {{ $session->end_time->format('H:i') }}
                            </div>
                            <x-status-badge :status="$session->status" />
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-10 text-center">
                        <div>
                            <div class="text-sm text-gray-500">Jarak</div>
                            <div class="font-semibold text-lg">{{ number_format($session->distance, 2) }} km</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Durasi</div>
                            <div class="font-semibold text-lg">{{ gmdate('H\j i\m', $session->duration_seconds) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Event</div>
                            <div class="font-semibold text-lg">{{ $session->events->count() }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Foto</div>
                            <div class="font-semibold text-lg">{{ $session->photos->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 flex min-h-0 gap-3 pb-2">
            <div class="flex-1 relative">
                <div id="map" class="w-full h-full"></div>
                <button x-show="!sidebarVisible" @click="toggleSidebar()"
                    class="absolute top-4 right-4 z-[1000] bg-white shadow-lg rounded-full w-12 h-12 ">
                    ☰
                </button>
            </div>

            <aside
                class="transition-all duration-300 overflow-hidden bg-white rounded-xl shadow-xl flex flex-col border border-gray-200"
                :class="sidebarVisible ? 'w-[420px]' : 'w-0'" x-show="sidebarVisible" x-transition>
                <template x-if="sidebarVisible">
                    <div class="h-full flex flex-col">
                        <!-- LIST MODE -->
                        <template x-if="sidebarMode === 'list'">
                            <div class="h-full flex flex-col">
                                <div class="p-4 border-b flex items-center justify-between">
                                    <div>
                                        <h2 class="text-lg font-semibold">Events</h2>
                                        <div class="text-xs text-gray-500">
                                            <span x-text="session.events.length"></span> Event
                                        </div>
                                    </div>
                                    <button @click="toggleSidebar()" class="w-8 h-8 rounded-lg hover:bg-gray-100">
                                        ✕
                                    </button>
                                </div>
                                <div class="flex-1 overflow-y-auto">
                                    <template x-for="(event,index) in session.events" :key="event.id">
                                        <button
                                            @click="selectedEvent = event; selectedPhotoIndex = 0; sidebarMode = 'detail'"
                                            class="w-full px-5 py-4 text-left transition border-b hover:bg-gray-50">
                                            <div class="flex items-center gap-4">
                                                <div class="w-8 h-8 rounded-full bg-red-500 text-white text-sm font-semibold flex items-center justify-center"
                                                    x-text="index + 1"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate"
                                                        x-text="event.title?.trim() ? event.title : 'Event ' + (index + 1)">
                                                    </div>
                                                    <div class="mt-1 text-sm text-gray-500"
                                                        x-text="formatDateTime(event.timestamp)">
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1 text-sm text-gray-500 shrink-0">
                                                    📷
                                                    <span x-text="event.photos?.length ?? 0"></span>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <!-- DETAIL MODE -->
                        <template x-if="sidebarMode === 'detail'">
                            <div class="h-full flex flex-col">
                                <div class="p-4 border-b flex items-center justify-between">
                                    <h2 @click="sidebarMode = 'list'"
                                        class="inline-flex items-center gap-2 text-lg font-semibold cursor-pointer transition-colors">
                                        ← Detail Event
                                    </h2>

                                    <button @click="toggleSidebar()" class="w-8 h-8 rounded-lg hover:bg-gray-100">
                                        ✕
                                    </button>
                                </div>
                                <div x-show="selectedEvent" class="flex-1 overflow-y-auto p-4">
                                    <!-- FOTO BESAR -->
                                    <template x-if="selectedEvent.photos?.length">
                                        <div>
                                            <div class="relative">
                                                <img :src="'/' + selectedEvent.photos[selectedPhotoIndex].file_path"
                                                    class="w-full h-56 object-cover rounded-xl">
                                                <div
                                                    class="absolute bottom-2 left-2 px-2 py-1 text-xs text-white bg-black/60 rounded">
                                                    <span x-text="selectedPhotoIndex + 1"></span>/<span
                                                        x-text="selectedEvent.photos.length"></span>
                                                </div>
                                                <button x-show="selectedPhotoIndex > 0" @click="selectedPhotoIndex--"
                                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-white rounded-lg shadow px-3 py-2">
                                                    ‹
                                                </button>
                                                <button x-show="selectedPhotoIndex < selectedEvent.photos.length - 1"
                                                    @click="selectedPhotoIndex++"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-white rounded-lg shadow px-3 py-2">
                                                    ›
                                                </button>
                                            </div>
                                            <div class="flex gap-2 mt-3 overflow-x-auto">
                                                <template x-for="(photo,index) in selectedEvent.photos"
                                                    :key="photo.id">
                                                    <img @click="selectedPhotoIndex = index" :src="'/' + photo.file_path"
                                                        class="w-16 h-16 object-cover rounded-lg border cursor-pointer"
                                                        :class="selectedPhotoIndex === index ? 'border-blue-500 border-2' :
                                                            'border-gray-200'">
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- TITLE -->
                                    <div class="mt-5">
                                        <h2 class="text-xl font-semibold"
                                            x-text="selectedEvent.title?.trim() ? selectedEvent.title : 'Event'"></h2>
                                    </div>
                                    <!-- TIMESTAMP -->
                                    <div class="mt-3 text-sm text-gray-500 flex items-center gap-2">
                                        📅
                                        <span x-text="formatDateTime(selectedEvent.timestamp)"></span>
                                    </div>
                                    <!-- KOORDINAT -->
                                    <div class="mt-3 text-sm text-gray-500 flex items-center gap-2">
                                        📍
                                        <span x-text="selectedEvent.latitude"></span>,
                                        <span x-text="selectedEvent.longitude"></span>
                                    </div>
                                    <!-- DESKRIPSI -->
                                    <div class="mt-6">
                                        <div class="font-medium mb-2">
                                            Deskripsi
                                        </div>
                                        <div class="text-gray-700"
                                            x-text="selectedEvent.description?.trim() ? selectedEvent.description : 'Belum ada deskripsi'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </aside>
        </main>

    </div>

    <script>
        function data() {
            return {
                sidebarVisible: true,
                sidebarMode: 'list',
                selectedEvent: null,
                selectedPhotoIndex: 0,
                session: @json($session),
                toggleSidebar() {
                    this.sidebarVisible = !this.sidebarVisible;
                    this.$nextTick(() => {

                        setTimeout(() => {

                            this.map.invalidateSize();

                        }, 300);

                    });
                },
                showSidebar() {
                    this.sidebarVisible = true;
                    this.$nextTick(() => {

                        setTimeout(() => {

                            this.map.invalidateSize();

                        }, 300);

                    });
                },
                initMap() {
                    this.map = L.map('map');

                    L.tileLayer(
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }
                    ).addTo(this.map);

                    console.log(
                        '[MAP] READY'
                    );
                    this.renderTrack()
                    this.renderEvents()
                },
                renderTrack() {
                    const coordinates = this.session.track_points.map(point => [
                        point.latitude, point.longitude
                    ])

                    const polyline = L.polyline(coordinates, {
                        weight: 5
                    }).addTo(this.map)

                    this.map.fitBounds(polyline.getBounds())
                },
                renderEvents() {

                    this.session.events.forEach((event, index) => {

                        const marker = L.marker(
                            [
                                event.latitude,
                                event.longitude
                            ], {
                                icon: L.divIcon({
                                    className: '',
                                    html: `
                        <div style="
                            background:#ef4444;
                            color:white;
                            width:32px;
                            height:32px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-weight:bold;
                            border:2px solid white;
                            box-shadow:0 2px 8px rgba(0,0,0,.3);
                        ">
                            ${index + 1}
                        </div>
                    `,
                                    iconSize: [32, 32],
                                    iconAnchor: [16, 16],
                                })
                            }
                        );

                        marker.addTo(this.map);

                        marker.on('click', () => {
                            this.showSidebar()
                            this.selectedEvent = event;
                            this.selectedPhotoIndex = 0;
                            this.sidebarMode = 'detail';

                        });

                    });

                },
                titleOrDefault(event, index) {
                    return event.title?.trim() ?
                        event.title :
                        `Event ${index + 1}`;
                },

                descriptionOrDefault(event) {
                    return event.description?.trim() ?
                        event.description :
                        'Tidak ada deskripsi';
                },
                formatDateTime(timestamp) {

                    const date = new Date(timestamp);

                    const datePart = date.toLocaleDateString(
                        'id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }
                    );

                    const timePart = date.toLocaleTimeString(
                        'id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }
                    );

                    return `${datePart} ${timePart}`;
                },
            }
        }
    </script>
@endsection
