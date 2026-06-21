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
    <div x-data="{ 
        viewMode: localStorage.getItem('lila_activities_view_mode') || 'grid',
        toggleView() {
            this.viewMode = this.viewMode === 'grid' ? 'table' : 'grid';
            localStorage.setItem('lila_activities_view_mode', this.viewMode);
        }
    }">
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-wide text-blue-700">Perjalanan</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Daftar Perjalanan</h1>
            <p class="mt-1 text-gray-500">Kelola, telusuri, dan buka detail perjalanan lapangan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button @click="toggleView()" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                <svg x-show="viewMode === 'grid'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                <svg x-show="viewMode === 'table'" style="display: none" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                <span x-text="viewMode === 'grid' ? 'Mode Tabel' : 'Mode Grid'">Mode View</span>
            </button>
            <a href="{{ url('dashboard') }}"
                class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Temuan</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_events']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Foto</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_photos']) }}</div>
        </div>
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
    </div>

    <form method="GET" action="{{ url('activities') }}"
        class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 xl:grid-cols-[1fr_120px_120px_180px_auto]">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari Perjalanan</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Nama perjalanan..."
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block flex items-end">
                <div class="flex items-center h-10 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="has_findings" value="1" @checked(request('has_findings')) class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-xs font-semibold text-gray-700 select-none">Hanya ada temuan</span>
                </div>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Bulan</span>
                <select name="month"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    @foreach (range(1, 12) as $monthOption)
                        <option value="{{ $monthOption }}" @selected($month === $monthOption)>
                            {{ \Carbon\Carbon::create(null, $monthOption)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tahun</span>
                <select name="year"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    @foreach ($years as $yearOption)
                        <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Urutkan</span>
                <select name="sort"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Terbaru</option>
                    <option value="events" @selected(request('sort') === 'events')>Temuan terbanyak</option>
                    <option value="photos" @selected(request('sort') === 'photos')>Foto terbanyak</option>
                    <option value="distance" @selected(request('sort') === 'distance')>Jarak terjauh</option>
                    <option value="duration" @selected(request('sort') === 'duration')>Durasi terlama</option>
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

    <!-- Grid View -->
    @php
        $maxDensity = $sessions->max(fn($s) => ($s->events_count * 10) + ($s->photos_count * 5) + $s->track_points_count);
        $maxDensity = max(1, $maxDensity);
    @endphp
    <div x-show="viewMode === 'grid'" class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
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
                            <div class="font-bold">{{ number_format($session->distance, 2) }} km</div>
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
                            $densityRaw = ($session->events_count * 10) + ($session->photos_count * 5) + $session->track_points_count;
                            $densityPercentage = ($densityRaw / $maxDensity) * 100;
                        @endphp
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ max(4, $densityPercentage) }}%"></div>
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

    <!-- Table View -->
    <div x-show="viewMode === 'table'" style="display: none;" class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Informasi Perjalanan</th>
                        <th class="px-6 py-4">Waktu Mulai</th>
                        <th class="px-6 py-4 text-center">Jarak</th>
                        <th class="px-6 py-4 text-center">Durasi</th>
                        <th class="px-6 py-4 text-center">Temuan</th>
                        <th class="px-6 py-4 text-center">Foto</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</div>
                                <div class="mt-1"><x-status-badge :status="$session->status" /></div>
                            </td>
                            <td class="px-6 py-4">{{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ number_format($session->distance, 2) }} km</td>
                            <td class="px-6 py-4 text-center">{{ $formatDuration($session->duration_seconds) }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ $session->events_count }}</td>
                            <td class="px-6 py-4 text-center">{{ $session->photos_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ url('activities', $session) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                                    Detail
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                Tidak ada perjalanan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($sessions->hasPages())
        <div class="mt-6">
            {{ $sessions->links() }}
        </div>
    @endif
    </div>
@endsection
