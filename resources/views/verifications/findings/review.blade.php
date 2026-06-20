@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ url('verifications/findings') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-gray-900 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Antrian
        </a>
    </div>

    <div class="mb-6 rounded-lg bg-indigo-600 px-6 py-4 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-200">Mode Review Sesi</div>
            <h1 class="text-xl font-bold mt-1">{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-xs text-indigo-200 font-medium">Progres Verifikasi</div>
                <div class="text-lg font-bold">{{ $totalCount - $remainingCount }} / {{ $totalCount }} Selesai</div>
            </div>
            <div class="w-32 h-2 rounded-full bg-indigo-900 overflow-hidden relative">
                <div class="absolute top-0 left-0 h-full bg-green-400 transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 mb-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                <h2 class="text-2xl font-bold text-gray-950">{{ $event->title ?? 'Temuan Tanpa Judul' }}</h2>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">ID: {{ Str::limit($event->id, 8) }}</span>
            </div>
            
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Waktu Temuan</div>
                    <div class="mt-2 font-bold text-gray-950">{{ optional($event->timestamp)->format('d M Y, H:i') ?? '-' }}</div>
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
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Deskripsi / Catatan Lapangan</div>
                <p class="mt-2 whitespace-pre-line leading-7 text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $event->description ?: 'Belum ada deskripsi temuan.' }}</p>
            </div>
        </section>

        <section class="flex flex-col gap-4">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm flex-1 min-h-[300px]">
                <div id="finding-map" class="h-full w-full"></div>
            </div>
        </section>
    </div>

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-gray-950 mb-4">Bukti Foto</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            @forelse ($event->photos as $photo)
                <a href="{{ url($photo->file_path) }}" target="_blank" class="block overflow-hidden rounded-lg border border-gray-200 bg-gray-50 group hover:border-blue-500 transition">
                    <div class="aspect-[4/3] bg-gray-100 relative">
                        @if ($photo->thumbnail_path || $photo->file_path)
                            <img src="{{ url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename ?? 'Foto temuan' }}"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-sm font-semibold text-gray-400">No Image</div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center">
                            <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">Belum ada foto yang dilampirkan untuk temuan ini.</div>
            @endforelse
        </div>
    </section>

    <!-- ACTION PANEL -->
    <div class="sticky bottom-6 z-50 rounded-xl border border-gray-200 bg-white/90 p-4 shadow-xl backdrop-blur flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <div class="font-bold text-gray-900">Keputusan Verifikasi</div>
            <div class="text-sm text-gray-500">Pilih tindakan untuk temuan ini, layar akan otomatis memuat temuan berikutnya.</div>
        </div>
        
        <div class="flex w-full md:w-auto items-center gap-3">
            <form action="{{ route('verifications.findings.verify', ['session' => $session->id, 'event' => $event->id]) }}" method="POST" class="flex-1 md:flex-none">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="reject">
                <button type="submit" onclick="return confirm('Tolak temuan ini?')" class="w-full md:w-32 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 shadow-sm hover:bg-rose-100 transition focus:ring-2 focus:ring-rose-500 outline-none">
                    Tolak
                </button>
            </form>

            <form action="{{ route('verifications.findings.verify', ['session' => $session->id, 'event' => $event->id]) }}" method="POST" class="flex-1 md:flex-none">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="verify">
                <button type="submit" class="w-full md:w-48 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 outline-none">
                    Verifikasi (Approve)
                </button>
            </form>
        </div>
    </div>

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
