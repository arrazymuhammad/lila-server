# Laporan Implementasi
## Iterasi 03: Verifikasi Temuan Pengamatan (A: Inti & B: Kategori)

* Ringkasan Implementasi
Seluruh kriteria fungsional, *visibility rules*, dan *UI rules* untuk Iterasi 03 (Fase A dan Fase B) telah diimplementasikan dengan sukses. Sistem kini mendukung proses antrian verifikasi, peninjauan iteratif (Flashcard Mode) dengan *auto-next*, serta perlindungan basis data (SyncController) dan visibilitas di halaman publik (Map & Dashboard). Khusus untuk Fase B, operator sekarang dapat menyematkan "Kategori Baku" pada setiap temuan menggunakan fitur *Auto-suggest* tanpa melanggar batasan modifikasi skema secara langsung.

* File Yang Diubah
1. `app/Models/ActivityEvent.php` (Memasukkan atribut `status` ke dalam array `$fillable`)
2. `app/Http/Controllers/Api/SyncController.php` (Penyesuaian sinkronisasi awal dan override protection)
3. `app/Http/Controllers/Verification/FindingController.php` (Pembuatan *controller* untuk Lobi Sesi, Mode Review, serta pengiriman saran *operator_category*)
4. `app/Http/Controllers/MapController.php` (Pembuatan filter visibilitas dan *toggle* marker temuan)
5. `app/Http/Controllers/FindingController.php` (Pembatasan relasi publik hanya untuk temuan *verified*)
6. `app/Http/Controllers/ActivityController.php` (Perbaikan filter statistik jumlah temuan dan foto per-bulan/tahun)
7. `app/Http/Controllers/DashboardController.php` (Sinkronisasi widget dasbor mengikuti *verified event*)
8. `resources/views/layouts/sidebar.blade.php` (Penambahan menu Verifikasi > Temuan)
9. `resources/views/maps/index.blade.php` (Penambahan *toggle* dan label visual *[Belum Diverifikasi]* pada marker)
10. `resources/views/activities/index.blade.php` (Penyempurnaan UI filter kalender dan *toggle table-grid view*)
11. `resources/views/verifications/findings/index.blade.php` (Blade baru untuk Lobi Antrian Sesi)
12. `resources/views/verifications/findings/review.blade.php` (Blade baru untuk Mode Review Flashcard dengan pemisahan input Judul Asli vs Kategori Baku)
13. `resources/views/findings/show.blade.php` (Penampilan `operator_category` di rincian temuan publik)

* Route Yang Ditambah
1. `GET /verifications/findings` (Lobi Sesi Verifikasi Temuan)
2. `GET /verifications/findings/{session}` (Masuk Mode Review untuk Sesi tertentu)
3. `PATCH /verifications/findings/{session}/events/{event}` (Aksi Verifikasi/Tolak pada sebuah temuan)

* Fitur Yang Berhasil Diimplementasikan
1. Lobi Antrian Sesi dengan jumlah temuan *submitted*.
2. Mode Review (Flashcard) dengan kemampuan *Auto-Next*, Peta Mini, dan Galeri Foto.
3. Penerimaan (Approve) atau Penolakan (Reject) temuan secara penuh, serta Penolakan foto secara parsial.
4. Filter dan Perlindungan *Visibility* (Temuan *submitted/rejected* tidak akan bocor ke daftar publik dan detail perjalanan publik).
5. Toggle Visibilitas di halaman Peta untuk memantau titik koordinat temuan *submitted* (tersimpan di *localStorage*).
6. Tampilan ganda (Grid dan Tabel) yang bisa diubah (toggle) pada halaman `/activities`.
7. **(Iterasi 03-B)**: Fitur input **Kategori Baku (Operator)** dengan bantuan *Auto-suggest Custom Dropdown* (berbasis TailwindCSS & Alpine.js) yang ditarik secara dinamis dari database (diurutkan secara alfabetis) tanpa memerlukan tabel kategori terpisah. Atribut `required` juga diterapkan untuk menjamin kelengkapan data.

* Deviasi Dari Iterasi
1. Menambahkan validasi ketat (pesan error `$errors`) jika operator melakukan *Approve* tetapi menolak semua bukti foto yang ada, guna memastikan kualitas temuan yang diterima publik.
2. Penambahan filter kalender (Bulan & Tahun) serta mode Tabel di daftar Perjalanan (`/activities`) sesuai permintaan *QA Notes* secara *ad-hoc*.
3. **(Iterasi 03-B)**: Penyimpanan `operator_category` dilakukan menggunakan teknik penugasan properti eksplisit (*bypassing mass assignment*) karena agen tidak memiliki kewenangan mengubah `$fillable` Model secara permanen.

* Risiko Yang Masih Ada / Catatan Iterasi Berikutnya
1. **Sistem Manajemen Kategori:** Sistem kategori dinamis saat ini masih mengandalkan pengetikan manual. Ini berisiko memunculkan duplikasi kategori akibat salah ketik. **Sistem CRUD (tabel master beserta halaman manajemennya) untuk Kategori Baku sangat direkomendasikan menjadi prioritas pada iterasi berikutnya.**
2. **Ketiadaan Mode Edit Lanjutan:** Saat ini proses verifikasi temuan bersifat *one-way* (Lobi Sesi → Mode Review). Temuan yang sudah disetujui (diverifikasi) tidak bisa diedit ulang. Hal ini berpotensi merepotkan jika operator melakukan kesalahan. Mode Edit (*Edit Mode*) untuk temuan yang sudah diverifikasi sangat direkomendasikan untuk dibangun di iterasi berikutnya.
3. Temuan lama yang di-*reset* massal menjadi `submitted` mungkin akan membebani antrian Lobi Sesi untuk sementara waktu hingga diverifikasi tuntas oleh operator.
4. Tidak ada rekam jejak (*audit trail*) eksplisit tentang alasan penolakan spesifik temuan oleh operator.
