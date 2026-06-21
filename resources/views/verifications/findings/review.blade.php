@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

@section('content')
    <div x-data="{ isPhotoModalOpen: false, currentPhotoUrl: '', currentPhotoName: '' }">
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

        <form action="{{ route('verifications.findings.verify', ['session' => $session->id, 'event' => $event->id]) }}" method="POST">
            @csrf
            @method('PATCH')
            
            @error('action')
                <div class="mb-6 rounded-lg bg-rose-50 p-4 border border-rose-200">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-semibold text-rose-800">{{ $message }}</span>
                    </div>
                </div>
            @enderror

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 mb-6">
                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2 flex flex-col">
                    <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-4 gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Judul / Kategori Temuan</label>
                            <input type="text" name="title" value="{{ $event->title }}" required class="w-full text-xl font-bold text-gray-950 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 shadow-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-4 border border-gray-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Waktu Temuan</div>
                            <div class="mt-2 font-bold text-gray-950">{{ optional($event->timestamp)->format('d M Y, H:i') ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 border border-gray-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Koordinat</div>
                            <div class="mt-2 font-bold text-gray-950">
                                {{ $event->latitude && $event->longitude ? number_format($event->latitude, 5) . ', ' . number_format($event->longitude, 5) : '-' }}
                            </div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-4 border border-gray-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Foto</div>
                            <div class="mt-2 font-bold text-gray-950">{{ $event->photos->count() }}</div>
                        </div>
                    </div>

                    <div class="mt-6 flex-1 flex flex-col">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Deskripsi / Catatan Lapangan</label>
                        <textarea name="description" rows="5" class="w-full flex-1 rounded-lg border-gray-300 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">{{ $event->description }}</textarea>
                        <p class="text-xs text-gray-400 mt-2">Anda dapat memperbaiki salah eja atau menyesuaikan judul dan deskripsi sebelum menyetujui temuan.</p>
                    </div>
                </section>

                <section class="flex flex-col gap-4">
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm flex-1 min-h-[300px]">
                        <div id="finding-map" class="h-full w-full"></div>
                    </div>
                </section>
            </div>

            <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-950">Bukti Foto</h2>
                    <span class="text-sm text-gray-500">Centang kotak merah untuk menolak foto spesifik.</span>
                </div>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
                    @forelse ($event->photos as $photo)
                        <div class="relative group block overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition hover:border-indigo-500 shadow-sm">
                            <button type="button" 
                                @click="isPhotoModalOpen = true; currentPhotoUrl = '{{ url($photo->file_path) }}'; currentPhotoName = '{{ $photo->filename ?? 'Foto' }}'" 
                                class="w-full text-left focus:outline-none">
                                <div class="aspect-[4/3] bg-gray-100 relative">
                                    @if ($photo->thumbnail_path || $photo->file_path)
                                        <img src="{{ url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename ?? 'Foto temuan' }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm font-semibold text-gray-400">No Image</div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 drop-shadow-md transition" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                            
                            <!-- Reject Photo Checkbox -->
                            <div class="absolute top-2 right-2 z-10 bg-white/90 backdrop-blur-sm rounded-lg p-1.5 shadow border border-gray-200 flex items-center gap-1.5 cursor-pointer hover:bg-rose-50 transition" title="Tolak foto ini">
                                <input type="checkbox" name="rejected_photos[]" value="{{ $photo->id }}" class="h-4 w-4 rounded border-gray-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wide">Tolak</span>
                            </div>
                        </div>
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
                    <button type="submit" name="action" value="reject" onclick="return confirm('Tolak temuan ini secara keseluruhan?')" class="w-full md:w-32 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 shadow-sm hover:bg-rose-100 transition focus:ring-2 focus:ring-rose-500 outline-none">
                        Tolak
                    </button>

                    <button type="submit" name="action" value="verify" class="w-full md:w-48 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 outline-none">
                        Verifikasi (Approve)
                    </button>
                </div>
            </div>
        </form>

        <!-- PHOTO MODAL -->
        <div x-show="isPhotoModalOpen" style="display: none" class="relative z-[9999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isPhotoModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-[9999] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="isPhotoModalOpen" 
                         @click.away="isPhotoModalOpen = false"
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-xl bg-transparent text-left transition-all sm:my-8 sm:w-full sm:max-w-4xl shadow-2xl">
                        
                        <div class="absolute top-4 right-4 z-10">
                            <button type="button" @click="isPhotoModalOpen = false" class="rounded-full bg-black/50 p-2 text-white hover:bg-black/70 focus:outline-none transition backdrop-blur-md">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <img :src="currentPhotoUrl" :alt="currentPhotoName" class="w-full h-auto max-h-[85vh] object-contain bg-black/20 rounded-xl">
                        
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-center">
                            <span class="text-white font-medium text-sm drop-shadow-md" x-text="currentPhotoName"></span>
                        </div>
                    </div>
                </div>
            </div>
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
