# Laporan Implementasi
## Iterasi 04: Reorientasi UI Observation-Centric

* Ringkasan Implementasi
Seluruh kriteria antarmuka dan *query* pengurutan di area Dashboard dan halaman Daftar Perjalanan (Activities) telah diubah agar selaras dengan orientasi pengamatan (Observation-Centric). LILA kini mendepankan dan menyorot metrik berbasis temuan (events) dan foto, meredam ketergantungan metrik lama yang sepenuhnya terpatok pada jarak atau jumlah titik *track points*.

* File Yang Diubah
1. `app/Http/Controllers/DashboardController.php` (Penggantian referensi tren dari jarak ke temuan, normalisasi skala tren, serta penyesuaian kriteria sorotan Widget Hero).
2. `app/Http/Controllers/ActivityController.php` (Penyematan filter query tambahan `has_findings` melalui fungsi `whereHas`).
3. `resources/views/dashboard.blade.php` (Perombakan grid *Summary Cards*, modifikasi pelabelan dan bobot grafik tren 7 hari, serta reposisi nilai di dalam widget Highlight Session).
4. `resources/views/activities/index.blade.php` (Perombakan grid *Summary Cards*, penambahan checkbox filter, penyusunan opsi dropdown sort baru, serta kalkulasi *progress bar* dinamis/kepadatan perjalanan berdasarkan temuan).

* Route Yang Ditambah
Tidak ada perubahan maupun penambahan Route. Semuanya masih berjalan di atas *endpoint* `/dashboard` dan `/activities` yang sudah ada.

* Fitur Yang Berhasil Diimplementasikan
1. Pengaturan ulang taktik render *Summary Cards* yang sekarang menyorot Temuan dan Foto di peringkat satu dan dua.
2. Transformasi batang Grafik 7 Hari agar memproyeksikan frekuensi jumlah temuan, bukan laju kilometer jarak.
3. Reposisi Widget Pahlawan (*Hero*) Dashboard supaya secara otomatis merekomendasikan sesi perjalanan terkaya temuan.
4. Input Pencarian baru berupa spesifik "Hanya tampilkan perjalanan dengan temuan" (`has_findings`) yang tersambung langsung pada level Eloquent Builder.
5. Pembaruan parameter skala pengukur (*progress bar*) kepadatan sesi perjalanan yang kini dikalikan dengan beban ekstra untuk kejadian penemuan (x10) dan pendokumentasian (x5).

* Deviasi Dari Iterasi
1. Pada `dashboard.blade.php` *Widget Hero*, elemen kolom *Temuan* yang berdekatan dengan kolom *Jarak* sempat muncul dua kali di instruksi referensi, namun sudah diperbaiki menjadi perbandingan murni: Temuan, Jarak, Foto.
2. Penentuan batas maksimum kepadatan (*maximum density ratio*) di halaman `/activities` diolah seketika (secara *on-the-fly* via Blade `@php`) agar dapat menangani kondisi di mana seluruh temuan bernilai nol, tanpa perlu membebani logika Controller berlebihan.

* Risiko Yang Masih Ada
1. Karena reorientasi UI ini berfokus pada hasil temuan yang "terverifikasi", data pada grafik tren atau kartu ringkasan bisa terkesan drastis/rendah (bahkan kosong) apabila belum banyak temuan yang berhasil menyelesaikan antrean verifikasi di Iterasi 03.
2. Pada skala yang amat masif di masa mendatang, komputasi `withCount` harian untuk *events* dalam agregasi `DashboardController` dapat memengaruhi latensi; walau di tingkat yang ada sekarang (perjalanan sebatas hitungan normal), dampaknya sangat nihil.
