# ITERATION_02.md

## Visibility Rule & Sentralisasi Verifikasi

---

## 1. Latar Belakang

Pada Iterasi 1, mekanisme verifikasi perjalanan berhasil diterapkan, memungkinkan operator mengubah status `submitted` menjadi `verified` atau `rejected`. 

Namun, dari evaluasi QA terdapat dua temuan krusial:
1. Panel verifikasi saat ini berada di dalam Halaman Detail Perjalanan. Hal ini membuat proses pengecekan untuk puluhan/ratusan perjalanan menjadi tidak efisien (operator harus buka-tutup halaman detail satu per satu).
2. Data mentah (`submitted`) dan data bermasalah (`rejected`) masih tercampur baur dengan data sah (`verified`) di Dashboard, Peta, dan Daftar Perjalanan utama. 

Sesuai Roadmap, aplikasi harus mulai memisahkan data mentah dan menjadikan **data terverifikasi sebagai satu-satunya sumber utama untuk dashboard dan analisis**.

---

## 2. Masalah yang Diselesaikan

**Masalah utama:**
- Pengguna biasa (atau publik) masih melihat data kotor di dashboard dan peta, sehingga statistik tidak akurat/terverifikasi.
- Proses verifikasi oleh operator lambat dan rentan *fatigue* akibat alur klik (UX) yang terlalu dalam di halaman detail.

**Akibat dari masalah ini:**
- Kualitas data untuk pelaporan tidak bisa dipertanggungjawabkan karena mencampur data valid dan tidak valid.
- Halaman detail perjalanan kehilangan esensinya sebagai halaman "pengamatan murni" karena disisipi form aksi.

---

## 3. Tujuan Iterasi

1. **Menerapkan Visibility Rule**: Membatasi semua *query* data untuk Dashboard, Peta, Daftar Perjalanan, dan Daftar Temuan agar **secara eksklusif hanya memuat data dengan status `verified`**.
2. **Sentralisasi Verifikasi**: Memindahkan fitur verifikasi dari Halaman Detail Perjalanan ke halaman daftar tabel khusus (tabular) yang dirancang murni untuk *rapid checking* (pengecekan cepat).
3. **Mengembalikan Halaman Detail menjadi Read-Only**: Membersihkan seluruh form interaktif dari halaman detail.

---

## 4. Ruang Lingkup

### 4.1 Visibility Rule (Pembatasan Visibilitas Data)
- **DashboardController**: Ubah query agregasi dan *recent feeds* untuk hanya menghitung/menampilkan `TrackingSession` berstatus `verified`.
- **MapController**: Peta hanya me-render *polyline* dan marker dari session berstatus `verified`.
- **FindingController**: Daftar temuan dan pencarian hanya mencari *events* yang induk session-nya `verified`.
- **ActivityController (Halaman Utama)**: Hapus opsi filter status dari antarmuka pengguna; daftar ini secara *default* dan permanen hanya menampilkan `verified`.

### 4.2 Halaman Verifikasi Tersendiri
- Buat halaman/rute baru (contoh: `/verifications`) yang berwujud tabel.
- Halaman ini memuat daftar perjalanan dengan status `submitted` dan `rejected`.
- Di setiap baris tabel, sediakan tombol **Approve (Verifikasi)** dan **Reject (Tolak)** beserta form alasannya (sebaiknya dalam bentuk *modal* atau *inline UI* menggunakan Alpine.js).
- Update URL dari menu "Verifikasi" di Sidebar agar mengarah ke halaman tabel baru ini.

### 4.3 Pembersihan Halaman Detail (`/activities/{session}`)
- Hapus panel, tombol, dan logika verifikasi dari file `show.blade.php`.
- Halaman Detail kembali murni sebagai halaman pembacaan peta dan galeri foto (*read-only*).

---

## 5. Yang Tidak Termasuk Dalam Iterasi Ini

- Refactor menggunakan Model Global Scope yang agresif (lakukan lokalisasi `where('status', 'verified')` pada Controller saja agar lebih terkontrol).
- Mengubah struktur *database*, membuat tabel baru, atau migration.
- Otentikasi pengguna dan perlindungan hak akses (RBAC).
- Modifikasi format foto, thumbnail, atau sinkronisasi API Mobile.

---

## 6. Kriteria Selesai

Iterasi dinyatakan selesai apabila seluruh kondisi berikut terpenuhi:

- [ ] Dashboard (total, statistik, tren, highlight) **hanya** menghitung dan menampilkan perjalanan yang `verified`.
- [ ] Peta semua rute (`/map`) **hanya** menampilkan garis rute dari perjalanan yang `verified`.
- [ ] Daftar Temuan (`/findings`) **hanya** menampilkan temuan dari perjalanan yang `verified`.
- [ ] Daftar Perjalanan Utama (`/activities`) tidak lagi memiliki filter status (atau tidak menampilkannya), dan **hanya** menampilkan perjalanan `verified`.
- [ ] Terdapat halaman Verifikasi tabular baru yang khusus menampilkan daftar `submitted`.
- [ ] Operator dapat mengeksekusi Verifikasi dan Penolakan langsung dari baris tabel di halaman Verifikasi tersebut tanpa harus masuk ke Halaman Detail.
- [ ] Halaman Detail Perjalanan telah dibersihkan dari panel verifikasi (*read-only mode*).
- [ ] Menu "Verifikasi" di sidebar mengarah lurus ke halaman antrian tabular tersebut.

---

## 7. Risiko dan Hal yang Perlu Diperhatikan

### 7.1 Penurunan Angka Statistik Secara Tiba-tiba
Begitu kode iterasi ini dideploy, angka total perjalanan di Dashboard mungkin akan merosot drastis karena sistem langsung menyembunyikan data `submitted` dan `rejected`. Ini bukan *bug*, melainkan hasil logis dari *Visibility Rule*. Harus dikomunikasikan secara naratif ke *stakeholders*.

### 7.2 Kebocoran Query (Data Leakage)
Kita harus meneliti semua metode di Controller. *Eager Loading* seperti `withCount('events')` atau `with('trackPoints')` harus dipastikan dipanggil dari induk (`TrackingSession`) yang berstatus `verified`. Kegagalan melakukan ini akan menyebabkan data *kotor* tetap tampil di beberapa sudut aplikasi.

### 7.3 State Management pada Tabel Verifikasi
Karena eksekusi status dilakukan dari tabel, idealnya baris yang sudah di-*approve/reject* segera hilang atau berubah warna tanpa harus me-refresh seluruh halaman (untuk menjaga UX *rapid checking*). Implementasi dengan *Alpine.js* atau *Livewire* (jika ada) sangat disarankan, atau sekadar *redirect back* sederhana jika dirasa cukup memadai.

---

## 8. Dampak terhadap Pengguna

### Operator Monitoring
**Sesudah Iterasi Ini:** Kecepatan verifikasi meningkat tajam karena mereka tidak perlu pindah-pindah halaman. Semua data tunggu tersaji berjejer di satu layar (tabular).

### Pengguna Umum / Pimpinan
**Sesudah Iterasi Ini:** Mereka bisa mempercayai angka yang tampil di Dashboard dan Peta. Rute yang ditampilkan di peta bebas dari *noise* data mentah petugas yang belum di-cek. Ini adalah lonjakan kualitas (Business Value) yang signifikan.
