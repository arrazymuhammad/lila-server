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
            <div class="text-sm font-semibold uppercase tracking-wide text-amber-600">Antrian</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Verifikasi Perjalanan</h1>
            <p class="mt-1 text-gray-500">Periksa dan setujui data lapangan yang belum diverifikasi.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('export.sessions') }}"
                class="inline-flex w-fit items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                Export CSV
            </a>
            <a href="{{ url('dashboard') }}"
                class="inline-flex w-fit items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Menunggu Verifikasi</div>
            <div class="mt-2 text-2xl font-bold text-amber-900">{{ number_format($summary['total_submitted']) }}</div>
        </div>
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Ditolak</div>
            <div class="mt-2 text-2xl font-bold text-rose-900">{{ number_format($summary['total_rejected']) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ url('verifications') }}"
        class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_140px_140px_180px_auto]">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari Perjalanan</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Nama perjalanan..."
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Status</span>
                <select name="status"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua (Submitted & Rejected)</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Dari Tanggal</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Sampai Tanggal</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <form id="bulk-verify-form" action="{{ route('verifications.bulk-verify') }}" method="POST">
        @csrf
        <div class="mb-3 flex items-center justify-between bg-blue-50 p-3 rounded-lg border border-blue-200" x-data="{ count: 0 }">
            <div class="text-sm font-semibold text-blue-900">
                <span>Aksi Massal (<span id="selected-count">0</span> terpilih)</span>
            </div>
            <div class="flex gap-2">
                <button type="submit" name="action" value="verify" onclick="return confirm('Verifikasi semua yang terpilih?')" class="rounded bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                    Approve Terpilih
                </button>
                <button type="submit" name="action" value="reject" onclick="let r = prompt('Alasan tolak massal:'); if(r) { this.form.append(new Input('reason', r)); return true; } return false;" class="rounded bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                    Tolak Terpilih
                </button>
            </div>
        </div>
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold w-10 text-center">
                            <input type="checkbox" onclick="document.querySelectorAll('.session-cb').forEach(c => c.checked = this.checked); document.getElementById('selected-count').innerText = document.querySelectorAll('.session-cb:checked').length;">
                        </th>
                        <th class="px-4 py-3 font-semibold">Perjalanan</th>
                        <th class="px-4 py-3 font-semibold text-center">Waktu Mulai</th>
                        <th class="px-4 py-3 font-semibold text-center">Jarak</th>
                        <th class="px-4 py-3 font-semibold text-center">Data</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-gray-50/50" x-data="{ showRejectForm: false }">
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="session_ids[]" value="{{ $session->id }}" class="session-cb rounded border-gray-300" onclick="document.getElementById('selected-count').innerText = document.querySelectorAll('.session-cb:checked').length;">
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 w-1/3">
                                <div>{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</div>
                                <div class="mt-1 text-xs text-gray-500 font-normal">ID: {{ Str::limit($session->id, 8) }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                {{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div>{{ number_format($session->distance, 2) }} km</div>
                                <div class="text-xs text-gray-400">{{ $formatDuration($session->duration_seconds) }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex justify-center gap-2 text-xs">
                                    <span class="rounded bg-blue-50 px-2 py-0.5 text-blue-700" title="Temuan">{{ $session->events_count }} T</span>
                                    <span class="rounded bg-indigo-50 px-2 py-0.5 text-indigo-700" title="Foto">{{ $session->photos_count }} F</span>
                                    <span class="rounded bg-gray-100 px-2 py-0.5 text-gray-700" title="Titik Lokasi">{{ $session->track_points_count }} P</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-status-badge :status="$session->status" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div x-show="!showRejectForm" class="flex justify-end items-center gap-2">
                                    <a href="{{ url('activities', $session) }}" target="_blank" class="rounded border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                        Lihat
                                    </a>
                                    <button type="button" @click="showRejectForm = true" class="rounded bg-white border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                        Tolak
                                    </button>
                                    <button form="single-verify-{{ $session->id }}" type="submit" onclick="return confirm('Verifikasi perjalanan ini?')" class="rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700">
                                        Approve
                                    </button>
                                </div>
                                <div x-show="showRejectForm" style="display: none;" class="w-64 float-right text-left bg-white border border-gray-200 p-2 rounded shadow-lg absolute right-12 z-10 mt-1">
                                    <div class="flex flex-col gap-2">
                                        <textarea id="reason-{{ $session->id }}" required rows="2" class="w-full text-xs rounded border border-gray-300 p-2 focus:border-rose-500 focus:ring-rose-500 outline-none" placeholder="Alasan tolak..."></textarea>
                                        <div class="flex justify-end gap-1">
                                            <button type="button" @click="showRejectForm = false" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50">Batal</button>
                                            <button type="button" onclick="document.getElementById('reject-reason-{{ $session->id }}').value = document.getElementById('reason-{{ $session->id }}').value; document.getElementById('single-reject-{{ $session->id }}').submit();" class="rounded bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">Tolak</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data perjalanan yang menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </form>

    @foreach ($sessions as $session)
        <form id="single-verify-{{ $session->id }}" action="{{ route('verifications.verify', $session) }}" method="POST" class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="verify">
        </form>
        <form id="single-reject-{{ $session->id }}" action="{{ route('verifications.verify', $session) }}" method="POST" class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="reason" id="reject-reason-{{ $session->id }}" value="">
        </form>
    @endforeach
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Perjalanan</th>
                        <th class="px-4 py-3 font-semibold text-center">Waktu Mulai</th>
                        <th class="px-4 py-3 font-semibold text-center">Jarak</th>
                        <th class="px-4 py-3 font-semibold text-center">Data</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-gray-50/50" x-data="{ showRejectForm: false }">
                            <td class="px-4 py-3 font-medium text-gray-900 w-1/3">
                                <div>{{ $session->title ?? 'Perjalanan Tanpa Nama' }}</div>
                                <div class="mt-1 text-xs text-gray-500 font-normal">ID: {{ Str::limit($session->id, 8) }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                {{ optional($session->start_time)->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div>{{ number_format($session->distance, 2) }} km</div>
                                <div class="text-xs text-gray-400">{{ $formatDuration($session->duration_seconds) }}</div>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex justify-center gap-2 text-xs">
                                    <span class="rounded bg-blue-50 px-2 py-0.5 text-blue-700" title="Temuan">{{ $session->events_count }} T</span>
                                    <span class="rounded bg-indigo-50 px-2 py-0.5 text-indigo-700" title="Foto">{{ $session->photos_count }} F</span>
                                    <span class="rounded bg-gray-100 px-2 py-0.5 text-gray-700" title="Titik Lokasi">{{ $session->track_points_count }} P</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-status-badge :status="$session->status" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div x-show="!showRejectForm" class="flex justify-end items-center gap-2">
                                    <a href="{{ url('activities', $session) }}" target="_blank" class="rounded border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                        Lihat
                                    </a>
                                    <button @click="showRejectForm = true" class="rounded bg-white border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                        Tolak
                                    </button>
                                    <form action="{{ route('verifications.verify', $session) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="verify">
                                        <button type="submit" onclick="return confirm('Verifikasi perjalanan ini?')" class="rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700">
                                            Approve
                                        </button>
                                    </form>
                                </div>
                                <div x-show="showRejectForm" style="display: none;" class="w-64 float-right text-left bg-white border border-gray-200 p-2 rounded shadow-lg absolute right-12 z-10 mt-1">
                                    <form action="{{ route('verifications.verify', $session) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="reject">
                                        <textarea name="reason" required rows="2" class="w-full text-xs rounded border border-gray-300 p-2 mb-2 focus:border-rose-500 focus:ring-rose-500 outline-none" placeholder="Alasan tolak..."></textarea>
                                        <div class="flex justify-end gap-1">
                                            <button type="button" @click="showRejectForm = false" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50">Batal</button>
                                            <button type="submit" class="rounded bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data perjalanan yang menunggu verifikasi.
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
