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
            <div class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Antrian Temuan</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Verifikasi Temuan</h1>
            <p class="mt-1 text-gray-500">Pilih perjalanan untuk mulai memverifikasi daftar temuannya.</p>
        </div>
        <a href="{{ url('dashboard') }}"
            class="inline-flex w-fit items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-6">
        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Perjalanan Menunggu Review</div>
            <div class="mt-2 text-2xl font-bold text-indigo-900">{{ number_format($summary['total_pending_sessions']) }}</div>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Total Temuan Submitted</div>
            <div class="mt-2 text-2xl font-bold text-amber-900">{{ number_format($summary['total_pending_findings']) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ url('verifications/findings') }}"
        class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto]">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari Perjalanan</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Ketik nama perjalanan..."
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Perjalanan Terverifikasi</th>
                        <th class="px-4 py-3 font-semibold text-center">Waktu Mulai</th>
                        <th class="px-4 py-3 font-semibold text-center">Menunggu Verifikasi</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-medium text-gray-900 w-1/3">
                                <div>{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</div>
                                <div class="mt-1 text-xs text-gray-500 font-normal">ID: {{ Str::limit($session->id, 8) }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                {{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ $session->pending_events_count }} Temuan
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('verifications.findings.review', $session) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                                    <span>Mulai Review</span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">Antrian Kosong</h3>
                                <p class="mt-1 text-sm text-gray-500">Tidak ada temuan yang perlu diverifikasi dari perjalanan yang valid.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($sessions->hasPages())
        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    @endif
@endsection
