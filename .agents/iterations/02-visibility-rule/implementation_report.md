# IMPLEMENTATION REPORT

## Ringkasan Implementasi

Implementasi untuk Iterasi 02 (Visibility Rule & Sentralisasi Verifikasi) telah berhasil diselesaikan secara menyeluruh melalui 2 fase Task. Seluruh *Visibility Rule* kini telah aktif—di mana publik dan pengguna hanya akan disuguhkan data perjalanan yang telah berstatus `verified` pada halaman *Dashboard*, Peta Utama, dan Daftar Perjalanan Utama. Proses verifikasi yang sebelumnya tersebar kini telah disentralisasi menjadi satu halaman antrian berbentuk tabular, sehingga memfasilitasi operator untuk mengaudit data (`submitted` maupun `rejected`) secara massal dan terpusat tanpa harus melakukan pengecekan mendalam ke halaman detail secara berulang.

## File Yang Diubah

1. `app/Http/Controllers/DashboardController.php`
2. `app/Http/Controllers/MapController.php`
3. `app/Http/Controllers/FindingController.php`
4. `app/Http/Controllers/ActivityController.php`
5. `resources/views/activities/index.blade.php`
6. `app/Http/Controllers/VerificationController.php` (Baru)
7. `routes/web.php`
8. `resources/views/verifications/index.blade.php` (Baru)
9. `resources/views/layouts/app.blade.php`
10. `resources/views/activities/show.blade.php`

## Route Yang Ditambah

- `GET /verifications` -> mengarah ke `VerificationController@index`.
- `PATCH /verifications/{session}/verify` -> mengarah ke `VerificationController@verify` (memindahkan fungsi rute aksi `verify` dari `activities` sebelumnya).

## Fitur Yang Berhasil Diimplementasikan

1. **Visibility Rule Backend:** Semua *query* di `DashboardController`, `MapController`, `FindingController`, dan `ActivityController` telah dilindungi dengan kondisi klausa eksklusif `where('status', 'verified')`.
2. **Pembersihan Visibility UI:** Menghapus fungsi filter dropdown *status* dari halaman daftar perjalanan pengguna.
3. **Sentralisasi Halaman Verifikasi:** Melahirkan kontroler dan tampilan halaman `Verifications` secara utuh berbentuk *table-data* beserta *inline Alpine.js action* Approve/Reject.
4. **Navigasi Terarah:** Menu "Verifikasi" di sidebar tidak lagi membawa filter, melainkan menjadi pintu utama menuju URL tunggal `/verifications`.
5. **Read-Only Detail:** Mereset UI di `/activities/{session}` kembali murni sebagai lembar tinjauan peta dan media tanpa ditumpangi panel validasi interaktif.

## Deviasi Dari Iterasi

- Pada bagian *controller* di Task 2, kita mengambil langkah inisiatif dengan memisahkan *logic* sentralisasi verifikasi ini menjadi `VerificationController` alih-alih memberatkannya pada `ActivityController` yang sudah terlalu membesar, hal ini ditempuh agar pemisahan ruang *business-logic* publik/read-only vs *approval-flow* tercapai dan bersih.

## Risiko Yang Masih Ada

1. **Statistik Drop Shock:** Sesuai iterasi, statistik publik dapat anjlok. Pengguna internal harus dibrifing perihal migrasi logika visibilitas *data-real* ini.
2. **Batas Limitasi Sync API Mobile:** Masalah mendasar pada `SyncController` terkait penumpukan ulang data jika user *resync*—statusnya dapat terset-ulang kembali ke `submitted` dengan tidak disengaja—tetap eksis. Diperlukan iterasi terpisah di masa mendatang untuk merefaktor logika penerimaan API Sinkronisasi.
3. **Unprotected Endpoint:** Walaupun rute telah dipisahkan, secara lapisan keamanan framework tidak ada proteksi autentikasi akses pada route `/verifications`, siapapun yang mengetahui URL rute tabel antrian masih dapat memeriksa daftar antrian dan merubahnya.
