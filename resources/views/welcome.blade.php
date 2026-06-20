@extends('layouts.landing')

@section('content')

<!-- HERO -->

<section class="bg-gradient-to-br from-blue-50 to-white">

    <div class="container mx-auto px-6 py-24">

        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <div>

                <div class="text-blue-600 font-semibold mb-3">
                    LILA
                </div>

                <h1 class="text-5xl font-bold leading-tight">
                    Lihat & Lapor!
                </h1>

                <p class="mt-6 text-lg text-gray-600">
                    Aplikasi pencatatan aktivitas lapangan berbasis GPS untuk dokumentasi yang akurat dan terverifikasi.
                </p>

                <div class="flex gap-4 mt-8">

                    <a href="#download" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                        Download APK
                    </a>

                    <a href="/activities" class="px-6 py-3 border rounded-xl hover:bg-gray-50">
                        Lihat Dashboard
                    </a>

                </div>

                <div class="grid grid-cols-3 gap-6 mt-10">

                    <div>
                        <div class="font-semibold">
                            📍 Tracking GPS
                        </div>
                        <div class="text-sm text-gray-500">
                            Akurat
                        </div>
                    </div>

                    <div>
                        <div class="font-semibold">
                            📸 Dokumentasi
                        </div>
                        <div class="text-sm text-gray-500">
                            Foto & Lokasi
                        </div>
                    </div>

                    <div>
                        <div class="font-semibold">
                            ☁️ Sinkronisasi
                        </div>
                        <div class="text-sm text-gray-500">
                            Cepat & Aman
                        </div>
                    </div>

                </div>

            </div>

            <div class="flex justify-center">

                <img
                    src="{{url('assets/img/lila6.png')}}"
                    class="max-w-sm sm:max-w-xl rounded-3xl shadow-2xl"
                >

            </div>

        </div>

    </div>

</section>

<!-- FITUR -->

<section class="py-24 bg-white">

    <div class="container mx-auto px-6">

        <div class="text-center">

            <h2 class="text-4xl font-bold">
                Fitur Utama
            </h2>

            <p class="mt-3 text-gray-500">
                Semua yang Anda butuhkan untuk aktivitas lapangan.
            </p>

        </div>

        <div class="grid md:grid-cols-3 gap-8 mt-14">

            <div class="p-8 rounded-2xl border shadow-sm">
                <div class="text-5xl mb-4">📍</div>
                <h3 class="font-semibold text-xl">
                    Tracking GPS
                </h3>
                <p class="mt-3 text-gray-500">
                    Mencatat jalur perjalanan secara otomatis dengan akurasi tinggi.
                </p>
            </div>

            <div class="p-8 rounded-2xl border shadow-sm">
                <div class="text-5xl mb-4">📸</div>
                <h3 class="font-semibold text-xl">
                    Dokumentasi Lapangan
                </h3>
                <p class="mt-3 text-gray-500">
                    Ambil foto, lokasi dan deskripsi aktivitas secara langsung.
                </p>
            </div>

            <div class="p-8 rounded-2xl border shadow-sm">
                <div class="text-5xl mb-4">☁️</div>
                <h3 class="font-semibold text-xl">
                    Sinkronisasi Data
                </h3>
                <p class="mt-3 text-gray-500">
                    Kirim aktivitas ke server untuk verifikasi dan penyimpanan.
                </p>
            </div>

        </div>

    </div>

</section>

<!-- SCREENSHOT -->

<section class="py-24 bg-gray-50">

    <div class="container mx-auto px-6">


        <div class="w-full">

            <img src="{{url('assets/img/lila4.png')}}" class="rounded-2xl shadow">

        </div>

    </div>

</section>

<!-- CARA KERJA -->

<section id="cara-kerja" class="py-24 bg-white">

    <div class="container mx-auto px-6">

        <div class="text-center">

            <h2 class="text-4xl font-bold">
                Cara Kerja
            </h2>

            <p class="mt-4 text-gray-500">
                Empat langkah sederhana untuk mendokumentasikan aktivitas lapangan.
            </p>

        </div>

        <div class="grid md:grid-cols-4 gap-10 mt-16">

            <div class="text-center relative">

                <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center mx-auto text-xl font-bold shadow-lg">
                    1
                </div>

                <h3 class="mt-6 text-xl font-semibold">
                    Mulai Perjalanan
                </h3>

                <p class="mt-3 text-gray-500">
                    Aktifkan GPS dan mulai merekam perjalanan lapangan.
                </p>

            </div>

            <div class="text-center relative">

                <div class="w-16 h-16 rounded-full bg-green-600 text-white flex items-center justify-center mx-auto text-xl font-bold shadow-lg">
                    2
                </div>

                <h3 class="mt-6 text-xl font-semibold">
                    Catat Temuan
                </h3>

                <p class="mt-3 text-gray-500">
                    Tambahkan foto, lokasi, dan deskripsi pada titik penting.
                </p>

            </div>

            <div class="text-center relative">

                <div class="w-16 h-16 rounded-full bg-purple-600 text-white flex items-center justify-center mx-auto text-xl font-bold shadow-lg">
                    3
                </div>

                <h3 class="mt-6 text-xl font-semibold">
                    Sinkronisasi
                </h3>

                <p class="mt-3 text-gray-500">
                    Unggah aktivitas ke server untuk penyimpanan dan evaluasi.
                </p>

            </div>

            <div class="text-center relative">

                <div class="w-16 h-16 rounded-full bg-yellow-500 text-white flex items-center justify-center mx-auto text-xl font-bold shadow-lg">
                    4
                </div>

                <h3 class="mt-6 text-xl font-semibold">
                    Verifikasi
                </h3>

                <p class="mt-3 text-gray-500">
                    Data ditinjau melalui dashboard web lengkap dengan peta dan dokumentasi.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- DOWNLOAD -->

<section id="download" class="py-24">

    <div class="container mx-auto px-6">

        <div class="bg-blue-600 rounded-3xl p-12 text-white">

            <div class="grid lg:grid-cols-2 gap-10 items-center">

                <div>

                    <h2 class="text-4xl font-bold">
                        Unduh Aplikasi LILA
                    </h2>

                    <p class="mt-4 text-blue-100">
                        Siap digunakan untuk aktivitas lapangan dan dokumentasi berbasis GPS.
                    </p>

                </div>

                <div class="text-right">

                    <a href="{{url('apps/lila_v1.3.apk')}}"
                        class="inline-block px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold">
                        Download APK
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
