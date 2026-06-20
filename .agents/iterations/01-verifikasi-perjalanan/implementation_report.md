# IMPLEMENTATION REPORT

## Ringkasan Implementasi

Implementasi fitur Verifikasi Perjalanan pada Iterasi 01 telah berhasil diselesaikan. Operator monitoring sekarang dapat melakukan verifikasi (menyetujui) atau menolak perjalanan yang masuk dari aplikasi mobile yang saat ini berstatus `submitted`. Antarmuka verifikasi telah ditambahkan ke halaman detail perjalanan tanpa mengganggu peta maupun fitur yang sudah ada. Tautan "Verifikasi" pada sidebar juga telah diaktifkan untuk memudahkan akses ke daftar perjalanan yang perlu ditinjau.

## File Yang Diubah

1. `routes/web.php`
2. `app/Http/Controllers/ActivityController.php`
3. `resources/views/layouts/app.blade.php`
4. `resources/views/activities/show.blade.php`

## Route Yang Ditambah

- `PATCH /activities/{session}/verify` -> mengarah ke `ActivityController@verify` dengan nama route `activities.verify`.

## Fitur Yang Berhasil Diimplementasikan

1. **Panel Verifikasi:** Menambahkan panel verifikasi pada halaman detail perjalanan (`/activities/{session}`) yang muncul eksklusif ketika status perjalanan adalah `submitted`.
2. **Aksi Verifikasi:** Tombol "Verifikasi" yang melakukan pembaruan langsung status perjalanan menjadi `verified`.
3. **Aksi Penolakan:** Tombol "Tolak" yang memunculkan form input alasan penolakan, sebelum akhirnya mengubah status menjadi `rejected` setelah dikonfirmasi.
4. **Validasi Alasan:** Alasan wajib diisi ketika operator memilih untuk menolak (reject), mencegah penolakan tanpa keterangan minimal bagi pengguna itu sendiri secara logis saat verifikasi, walau pun sesuai *scope* tidak direkam ke database.
5. **Aktivasi Navigasi Sidebar:** Mengaktifkan link sidebar "Verifikasi" menjadi *shortcut* menuju URL daftar perjalanan dengan query parameter status aktif `?status=submitted`.

## Deviasi Dari Iterasi

- Tidak ada penyimpangan signifikan dari rencana iterasi asli. Pembuatan panel konfirmasi secara mulus dilakukan dengan Alpine.js untuk menghadirkan pengalaman pengguna interaktif yang lebih baik tanpa navigasi antar halaman atau popup sistem default yang kasar.

## Risiko Yang Masih Ada

1. **Tertimpa oleh Sinkronisasi Mobile:** Fitur `POST /api/sync` saat ini disetel untuk memaksa `status = 'submitted'`. Data yang sebelumnya sudah diverifikasi atau ditolak bisa kembali menjadi `submitted` apabila petugas lapangan menyinkronkan data kembali. Hal ini merupakan limitasi logika *existing* di `SyncController` dan bukan cakupan dalam iterasi saat ini, tetapi perlu diwaspadai di sisi user dan dievaluasi untuk iterasi terkait Sync Service nanti.
2. **Ketiadaan *State Memory* Alasan Penolakan:** Alasan penolakan tidak direkam permanen ke database, sehingga tidak ada informasi pasca-penolakan perihal kenapa status ditolak apabila dipertanyakan kemudian hari.
3. **Ketiadaan Autentikasi Pengguna:** Mengingat belum ada layer otentikasi di aplikasi ini, setiap pengakses halaman dapat mengakses link tersebut dan merubah data status perjalanannya sesuka hati.
