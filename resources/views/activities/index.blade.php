@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold">Aktivitas</h1>
            <p class="text-gray-500">Riwayat aktivitas lapangan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        @foreach ($sessions as $session)
            <a href="{{ url('activities', $session) }}"
                class="block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">

                <div class="p-5">

                    <div class="flex items-start justify-between">

                        <div>

                            <h2 class="font-semibold text-lg">
                                {{ $session->title ?? 'Aktivitas Tanpa Nama' }}
                            </h2>

                            <div class="text-sm text-gray-500 mt-1">
                                {{ $session->start_time->format('d M Y') }}
                            </div>

                        </div>

                        <x-status-badge :status="$session->status" />

                    </div>

                    <div class="grid grid-cols-4 gap-4 mt-5">

                        <div>
                            <div class="text-xs text-gray-500">Jarak</div>
                            <div class="font-medium">
                                {{ number_format($session->distance, 2) }} km
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Durasi</div>
                            <div class="font-medium">
                                {{ gmdate('H:i', $session->duration_seconds) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Event</div>
                            <div class="font-medium">
                                {{ $session->events_count }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Foto</div>
                            <div class="font-medium">
                                {{ $session->photos_count }}
                            </div>
                        </div>

                    </div>

                </div>

            </a>
        @endforeach

    </div>
@endsection
