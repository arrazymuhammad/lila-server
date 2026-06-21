@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-wide text-rose-700">Temuan</div>
            <h1 class="mt-1 text-3xl font-bold text-gray-950">Daftar Temuan Pengamatan</h1>
            <p class="mt-1 text-gray-500">Telusuri hasil pengamatan lapangan dari seluruh perjalanan.</p>
        </div>
        <a href="{{ url('map') }}"
            class="inline-flex w-fit items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
            Buka Peta
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Temuan</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['total_findings']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dengan Foto</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['with_photos']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dengan Koordinat</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['with_coordinates']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Perjalanan Terkait</div>
            <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($summary['journeys_with_findings']) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ url('findings') }}"
        class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 gap-3 xl:grid-cols-[1fr_180px_220px_220px_auto]">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari Temuan</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Judul atau deskripsi..."
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</span>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</span>
                <select name="category"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') == $cat)>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Perjalanan</span>
                <select name="session_id"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua perjalanan</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" @selected(request('session_id') === $session->id)>
                            {{ $session->title ?? 'Perjalanan Tanpa Nama' }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Terapkan
                </button>
                <a href="{{ url('findings') }}"
                    class="inline-flex h-10 items-center rounded-lg border border-gray-200 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </div>
        <div class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">
            Filter kategori belum ditampilkan karena struktur data saat ini tidak memiliki kolom kategori temuan.
        </div>
    </form>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
        @forelse ($findings as $finding)
            <a href="{{ url('findings', $finding) }}"
                class="group rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Temuan Pengamatan</div>
                        <h2 class="mt-2 truncate text-lg font-bold text-gray-950">
                            {{ $finding->title ?? 'Temuan Tanpa Judul' }}
                        </h2>
                        <div class="mt-1 text-sm text-gray-500">
                            {{ optional($finding->timestamp)->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>
                    <div class="shrink-0 rounded-lg bg-rose-50 px-3 py-2 text-sm font-bold text-rose-700">
                        {{ $finding->photos_count }} foto
                    </div>
                </div>

                <p class="mt-4 line-clamp-3 text-sm leading-6 text-gray-600">
                    {{ $finding->description ?: 'Belum ada deskripsi temuan.' }}
                </p>

                <div class="mt-5 flex flex-wrap gap-2 text-xs text-gray-500">
                    <span class="rounded bg-gray-100 px-2 py-1">{{ $finding->session?->title ?? 'Perjalanan Tanpa Nama' }}</span>
                    <span class="rounded bg-gray-100 px-2 py-1">
                        {{ $finding->latitude && $finding->longitude ? number_format($finding->latitude, 5) . ', ' . number_format($finding->longitude, 5) : 'Koordinat kosong' }}
                    </span>
                </div>

                <div class="mt-4 text-sm font-semibold text-blue-700 group-hover:text-blue-900">Buka detail temuan</div>
            </a>
        @empty
            <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center">
                <div class="text-lg font-bold text-gray-950">Tidak ada temuan pengamatan</div>
                <p class="mt-1 text-sm text-gray-500">Coba ubah filter tanggal, perjalanan, atau kata kunci.</p>
            </div>
        @endforelse
    </div>

    @if ($findings->hasPages())
        <div class="mt-6">
            {{ $findings->links() }}
        </div>
    @endif
@endsection
