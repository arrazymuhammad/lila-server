@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <div class="mb-2 flex flex-wrap items-center gap-2 text-sm">
                <a href="{{ url('findings') }}" class="font-semibold text-blue-700 hover:text-blue-900">Daftar Temuan Pengamatan</a>
                <span class="text-gray-300">/</span>
                <span class="font-medium text-gray-500">Detail Temuan</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-950">{{ $event->title ?? 'Temuan Tanpa Judul' }}</h1>
            <p class="mt-1 text-gray-500">{{ optional($event->timestamp)->format('d M Y, H:i') ?? '-' }}</p>
        </div>
        @if ($event->session)
            <a href="{{ url('activities', $event->session) }}"
                class="inline-flex w-fit items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Buka Detail Perjalanan
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-bold text-gray-950">Detail Temuan</h2>
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Perjalanan</div>
                    <div class="mt-2 font-bold text-gray-950">{{ $event->session?->title ?? 'Perjalanan Tanpa Nama' }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori Baku</div>
                    <div class="mt-2 font-bold text-indigo-700">
                        @if($event->operator_category)
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">{{ $event->operator_category }}</span>
                        @else
                            <span class="text-gray-400 font-normal italic">Belum dikategorikan</span>
                        @endif
                    </div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Koordinat</div>
                    <div class="mt-2 font-bold text-gray-950">
                        {{ $event->latitude && $event->longitude ? number_format($event->latitude, 5) . ', ' . number_format($event->longitude, 5) : '-' }}
                    </div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Foto</div>
                    <div class="mt-2 font-bold text-gray-950">{{ $event->photos->count() }}</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Deskripsi</div>
                <p class="mt-2 whitespace-pre-line leading-7 text-gray-700">{{ $event->description ?: 'Belum ada deskripsi temuan.' }}</p>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div id="finding-map" class="h-80 w-full"></div>
        </section>
    </div>

    <section class="mt-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-gray-950">Foto Temuan</h2>
        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            @forelse ($event->photos as $photo)
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                    <div class="aspect-[4/3] bg-gray-100">
                        @if ($photo->thumbnail_path || $photo->file_path)
                            <img src="{{ url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename ?? 'Foto temuan' }}"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-sm font-semibold text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="truncate p-3 text-sm font-semibold text-gray-900">{{ $photo->filename ?? 'Foto temuan' }}</div>
                </div>
            @empty
                <div class="col-span-full rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">Belum ada foto untuk temuan ini.</div>
            @endforelse
        </div>
    </section>

    <script>
        const finding = @json($event);
        const map = L.map('finding-map');

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        if (finding.latitude && finding.longitude) {
            map.setView([finding.latitude, finding.longitude], 16);
            L.marker([finding.latitude, finding.longitude]).addTo(map);
        } else {
            map.setView([-2.5489, 118.0149], 5);
        }
    </script>
@endsection
