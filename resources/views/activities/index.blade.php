@extends('layouts.app')

@php
    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return "{$hours}j {$minutes}m";
    };
@endphp

@section('content')
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-wide text-blue-700">Perjalanan</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Daftar Perjalanan</h1>
            <p class="mt-1 text-gray-500">Kelola, telusuri, dan buka detail perjalanan lapangan.</p>
        </div>
        <a href="{{ url('dashboard') }}"
            class="inline-flex w-fit items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Perjalanan</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_sessions']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Jarak</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_distance'], 2) }} km</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Durasi</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ $formatDuration($summary['total_duration']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Temuan</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_events']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Foto</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_photos']) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ url('activities') }}"
        class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_180px_auto]">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari Perjalanan</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Nama perjalanan..."
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Urutkan</span>
                <select name="sort"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Terbaru</option>
                    <option value="distance" @selected(request('sort') === 'distance')>Jarak terjauh</option>
                    <option value="duration" @selected(request('sort') === 'duration')>Durasi terlama</option>
                    <option value="events" @selected(request('sort') === 'events')>Temuan terbanyak</option>
                    <option value="photos" @selected(request('sort') === 'photos')>Foto terbanyak</option>
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Terapkan
                </button>
                <a href="{{ url('activities') }}"
                    class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
        @forelse ($sessions as $session)
            <a href="{{ url('activities', $session) }}"
                class="group overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                <div class="border-b border-gray-100 bg-gradient-to-r from-slate-900 to-slate-800 p-5 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-300">
                                {{ optional($session->start_time)->format('d M Y, H:i') ?? 'Tanggal belum tersedia' }}
                            </div>
                            <h2 class="mt-2 truncate text-xl font-bold">
                            {{ $session->title ?? 'Perjalanan Tanpa Nama' }}
                            </h2>
                        </div>
                        <div class="shrink-0 rounded-lg bg-white/10 px-3 py-2 text-right">
                            <div class="text-xs text-slate-300">Jarak</div>
                            <div class="font-bold">{{ number_format($session->distance, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <x-status-badge :status="$session->status" />
                        <span class="text-xs font-semibold text-blue-700 group-hover:text-blue-900">Buka detail</span>
                    </div>

                    <div class="mt-5 grid grid-cols-4 gap-3 text-center">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-gray-500">Durasi</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">{{ $formatDuration($session->duration_seconds) }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-gray-500">Temuan</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">{{ $session->events_count }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-gray-500">Foto</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">{{ $session->photos_count }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-gray-500">Point</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">{{ $session->track_points_count }}</div>
                        </div>
                    </div>

                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-gray-100">
                        @php
                            $density = min(100, ($session->events_count + $session->photos_count + $session->track_points_count / 10));
                        @endphp
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ max(6, $density) }}%"></div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">Kepadatan data perjalanan berdasarkan temuan, foto, dan track point.</div>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center">
                <div class="text-lg font-bold text-gray-950">Tidak ada perjalanan ditemukan</div>
                <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci, status, atau urutan pencarian.</p>
                <a href="{{ url('activities') }}"
                    class="mt-4 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                    Tampilkan semua
                </a>
            </div>
        @endforelse
    </div>

    @if ($sessions->hasPages())
        <div class="mt-6">
            {{ $sessions->links() }}
        </div>
    @endif
@endsection
