# QA Checklist
## Iterasi 03-A — Verifikasi Temuan Pengamatan

---

## Functional Test

[x] Buka halaman `/verifications/findings` (Lobi Sesi) dan pastikan halaman memuat daftar perjalanan yang memiliki temuan berstatus `submitted`.
[x] Perjalanan dengan status `submitted` atau `rejected` (belum verified) **tidak boleh muncul** di Lobi Sesi — pastikan hanya perjalanan `verified` yang ditampilkan beserta antrian temaannya.
[x] Klik salah satu perjalanan di Lobi Sesi dan pastikan sistem masuk ke **Mode Review (Flashcard)** untuk perjalanan tersebut.
[x] Dalam Mode Review, pastikan setiap temuan menampilkan: **foto**, **peta titik koordinat**, **kategori/judul temuan**, dan **deskripsi**.
[x] Klik tombol **Verifikasi (Approve)** pada sebuah temuan dan pastikan sistem otomatis berpindah ke temuan berikutnya dalam sesi yang sama (Auto-Next).
[x] Klik tombol **Tolak (Reject)** pada sebuah temuan, pastikan ada konfirmasi atau form alasan, dan setelah aksi status berubah ke `rejected` dan sistem berpindah ke temuan berikutnya.
[x] Setelah **seluruh temuan** dalam satu sesi selesai diverifikasi, pastikan sistem memberikan konfirmasi atau kembali ke Lobi Sesi.
[x] Pastikan perjalanan yang seluruh temaannya sudah diverifikasi **tidak lagi muncul** di Lobi Sesi (antrian bersih).

## UI Test

[x] Pastikan halaman Lobi Sesi (`/verifications/findings`) menampilkan jumlah temuan yang menunggu verifikasi di setiap kartu/baris perjalanan.
[x] Pastikan Mode Review (Flashcard) berjalan lancar tanpa full-page refresh saat berpindah antar temuan (navigasi Auto-Next).
[x] Pastikan terdapat indikator progres di Mode Review, misalnya "Temuan 3 dari 12" agar operator tahu seberapa jauh progres verifikasinya.
[x] Periksa tampilan di sidebar — pastikan sub-menu "Temuan" di bawah menu "Verifikasi" sudah tampil dan dapat diklik.
[x] Pastikan badge atau angka penanda antrian di menu Verifikasi Temuan memperbarui jumlahnya setelah aksi approve/reject.

## Visibility Rule Test

[x] Buka Daftar Temuan (`/findings`) dan pastikan **hanya** menampilkan temuan dengan status `verified` — tidak ada temuan `submitted` atau `rejected` yang tampil.
[x] Buka Peta Rute Utama (`/map`) dan pastikan secara **default** hanya marker temuan `verified` yang ditampilkan.
[x] Di halaman Peta, aktifkan **toggle "Tampilkan Semua Temuan"** dan pastikan marker temuan `submitted` ikut muncul (dengan penanda visual berbeda, misalnya warna abu-abu).
[x] Nonaktifkan toggle tersebut dan pastikan marker `submitted` hilang kembali.
[x] Refresh halaman peta setelah mengaktifkan toggle — pastikan preferensi toggle tersimpan di `localStorage` dan tidak kembali ke mode default.

## SyncController & Override Protection Test

[*TO BE TESTED*] *(Opsional — jika ada akses Postman)* Kirim ulang data sinkronisasi untuk sesi yang sudah memiliki temuan `verified`. Pastikan temuan yang `verified` **tidak** kembali menjadi `submitted` setelah resync.
[*TO BE TESTED*] *(Opsional)* Temuan yang `submitted` namun sudah ada di database, setelah resync dari mobile, tetap berstatus `submitted` (tidak berubah).

## Regression Test

[x] Buka Dashboard dan pastikan statistik (total temuan, dll.) masih memuat dengan benar dan tidak menampilkan error.
[x] Buka Daftar Perjalanan (`/activities`) dan pastikan filter serta tampilan data perjalanan `verified` masih berjalan normal.
[x] Buka halaman Detail Perjalanan (`/activities/{session}`) dan pastikan peta rute dan galeri foto temuan masih tampil dengan benar.
[x] Buka halaman `/verifications` (antrian verifikasi perjalanan) dan pastikan tidak ada gangguan akibat penambahan routing verifikasi temuan.

## Acceptance Criteria

[x] Kolom `status` tersedia di tabel `activity_events` dan digunakan oleh sistem.
[x] `SyncController` mengisi `status = 'submitted'` untuk events baru tanpa meng-override status yang sudah `verified`.
[x] Terdapat halaman Lobi Sesi (`/verifications/findings`) yang menampilkan perjalanan dengan antrian temuan yang belum diverifikasi.
[x] Operator dapat membuka Mode Review per-sesi dan melakukan Approve/Reject pada setiap temuan.
[x] Halaman Mode Review menampilkan foto, peta koordinat, kategori, dan deskripsi temuan.
[x] Ada mekanisme navigasi Auto-Next setelah setiap aksi verifikasi.
[x] Daftar Temuan umum (`/findings`) hanya menampilkan temuan `verified`.
[x] Peta memiliki toggle: strict (hanya verified) vs semua temuan, dengan state tersimpan di `localStorage`.
[x] Sidebar menampilkan navigasi ke verifikasi temuan.
[x]  Tidak ada perubahan pada API mobile atau kontrak sinkronisasi.


