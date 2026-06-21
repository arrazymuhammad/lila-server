@extends('layouts.app')

@php
    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return "{$hours}j {$minutes}m";
    };

    $statusLabels = [
        'submitted' => 'Submitted',
        'verified' => 'Verified',
        'rejected' => 'Rejected',
        'unknown' => 'Unknown',
    ];
@endphp

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-wide text-blue-700">LILA WebGIS</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Dashboard Operasional</h1>
            <p class="mt-1 text-gray-500">Ringkasan perjalanan lapangan, temuan pengamatan, dokumentasi, dan status verifikasi data.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ url('activities') }}"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Lihat Perjalanan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Temuan Pengamatan</div>
                    <div class="mt-2 text-3xl font-bold text-gray-950">{{ number_format($stats['total_events']) }}</div>
                </div>
                <div class="rounded-lg bg-rose-50 px-3 py-2 text-sm font-bold text-rose-700">EV</div>
            </div>
            <div class="mt-4 text-sm text-gray-500">{{ number_format($stats['events_per_session'], 1) }} temuan rata-rata per perjalanan</div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">Foto</div>
                    <div class="mt-2 text-3xl font-bold text-gray-950">{{ number_format($stats['total_photos']) }}</div>
                </div>
                <div class="rounded-lg bg-amber-50 px-3 py-2 text-sm font-bold text-amber-700">PH</div>
            </div>
            <div class="mt-4 text-sm text-gray-500">{{ number_format($stats['selected_photos']) }} foto terpilih</div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Perjalanan</div>
                    <div class="mt-2 text-3xl font-bold text-gray-950">{{ number_format($stats['total_sessions']) }}</div>
                </div>
                <div class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-bold text-blue-700">S</div>
            </div>
            <div class="mt-4 text-sm text-gray-500">{{ number_format($stats['avg_distance'], 2) }} km rata-rata per perjalanan</div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Jarak Tempuh</div>
                    <div class="mt-2 text-3xl font-bold text-gray-950">{{ number_format($stats['total_distance'], 2) }} km</div>
                </div>
                <div class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700">KM</div>
            </div>
            <div class="mt-4 text-sm text-gray-500">{{ $formatDuration($stats['total_duration']) }} total durasi lapangan</div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-950">Tren 7 Hari</h2>
                    <p class="text-sm text-gray-500">Jumlah temuan dan perjalanan berdasarkan tanggal mulai.</p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <div class="font-semibold text-gray-900">{{ number_format($activityTrend->sum('events_count')) }} temuan</div>
                    <div>{{ $activityTrend->sum('sessions') }} perjalanan</div>
                </div>
            </div>

            <div class="flex h-52 items-end gap-3">
                @foreach ($activityTrend as $day)
                    @php
                        $height = $day['events_count'] > 0 ? max(14, ($day['events_count'] / $maxTrendEvents) * 100) : 4;
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex h-36 w-full items-end rounded bg-gray-50 px-1">
                            <div class="w-full rounded-t bg-blue-600 transition" style="height: {{ $height }}%"></div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs font-semibold text-gray-700">{{ $day['label'] }}</div>
                            <div class="text-[11px] text-gray-500">{{ number_format($day['events_count']) }} temuan</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-950">Kesehatan Data</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Track Point</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($stats['total_track_points']) }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Avg Durasi</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ $formatDuration($stats['avg_duration']) }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Foto/Perjalanan</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($stats['photos_per_session'], 1) }}</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Temuan/Perjalanan</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($stats['events_per_session'], 1) }}</div>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($statusSummary as $status)
                    @php
                        $statusKey = $status->status ?? 'unknown';
                        $percentage = $stats['total_sessions'] > 0 ? ($status->total / $stats['total_sessions']) * 100 : 0;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $statusLabels[$statusKey] ?? ucfirst($statusKey) }}</span>
                            <span class="text-gray-500">{{ $status->total }} perjalanan</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-slate-800" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-500">Belum ada status perjalanan.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="rounded-lg border border-gray-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="text-lg font-bold text-gray-950">Perjalanan Terbaru</h2>
                <a href="{{ url('activities') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Perjalanan</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold text-right">Jarak</th>
                            <th class="px-5 py-3 font-semibold text-right">Durasi</th>
                            <th class="px-5 py-3 font-semibold text-right">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($latestActivities as $session)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <a href="{{ url('activities', $session) }}" class="font-semibold text-gray-950 hover:text-blue-700">
                                        {{ $session->title ?? 'Perjalanan Tanpa Nama' }}
                                    </a>
                                    <div class="mt-1 text-xs text-gray-500">{{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$session->status" />
                                </td>
                                <td class="px-5 py-4 text-right font-medium text-gray-800">{{ number_format($session->distance, 2) }} km</td>
                                <td class="px-5 py-4 text-right text-gray-600">{{ $formatDuration($session->duration_seconds) }}</td>
                                <td class="px-5 py-4 text-right text-gray-600">
                                    {{ $session->events_count }} temuan / {{ $session->photos_count }} foto
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada perjalanan terekam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-950">Perjalanan Terkaya Temuan</h2>
            @if ($highlightSession)
                <div class="mt-4 rounded-lg bg-slate-900 p-5 text-white">
                    <div class="text-sm text-slate-300">{{ optional($highlightSession->start_time)->format('d M Y') ?? '-' }}</div>
                    <div class="mt-2 text-xl font-bold">{{ $highlightSession->title ?? 'Perjalanan Tanpa Nama' }}</div>
                    <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-xs text-slate-400">Temuan</div>
                            <div class="font-semibold text-rose-400">{{ $highlightSession->events_count }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Jarak</div>
                            <div class="font-semibold">{{ number_format($highlightSession->distance, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">Foto</div>
                            <div class="font-semibold">{{ $highlightSession->photos_count }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-500">Belum ada perjalanan.</div>
            @endif

            <h2 class="mt-6 text-lg font-bold text-gray-950">Temuan Pengamatan Terbaru</h2>
            <div class="mt-3 space-y-3">
                @forelse ($latestEvents as $event)
                    <div class="rounded-lg border border-gray-100 p-3">
                        <div class="font-semibold text-gray-900">{{ $event->title ?? 'Temuan Tanpa Judul' }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ optional($event->timestamp)->format('d M Y, H:i') ?? '-' }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $event->session?->title ?? 'Perjalanan Tanpa Nama' }}</div>
                    </div>
                @empty
                    <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-500">Belum ada temuan pengamatan.</div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-950">Dokumentasi Terbaru</h2>
            <div class="text-sm text-gray-500">{{ number_format($stats['total_photos']) }} foto tersimpan</div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            @forelse ($latestPhotos as $photo)
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                    <div class="aspect-[4/3] bg-gray-100">
                        @if ($photo->thumbnail_path || $photo->file_path)
                            <img src="{{ url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename ?? 'Foto aktivitas' }}"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-sm font-semibold text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="truncate text-sm font-semibold text-gray-900">{{ $photo->filename ?? 'Foto aktivitas' }}</div>
                        <div class="mt-1 truncate text-xs text-gray-500">{{ $photo->session?->title ?? 'Perjalanan Tanpa Nama' }}</div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">Belum ada dokumentasi foto.</div>
            @endforelse
        </div>
    </section>
@endsection
