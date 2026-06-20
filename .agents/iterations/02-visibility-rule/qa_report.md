# QA Report: Iterasi 02 - Visibility Rule & Sentralisasi Verifikasi

## Status
**PASS WITH NOTES**

## Ringkasan Pengujian
Pengujian manual untuk Iterasi 02 dilakukan secara menyeluruh mencakup aspek fungsional, UI, regresi, dan kriteria penerimaan. Seluruh fitur utama berhasil berjalan sesuai spesifikasi: Visibility Rule aktif di semua modul (Dashboard, Peta, Daftar Perjalanan, Temuan), halaman antrian Verifikasi tabular berfungsi dengan baik sebagai pusat kontrol operator, dan Halaman Detail Perjalanan telah kembali menjadi mode *read-only* tanpa panel interaktif. Namun, terdapat 3 temuan UX/desain dari tester yang memerlukan pertimbangan untuk iterasi berikutnya.

## Hasil Checklist

### Functional Test (8/8)
- [x] Filter status telah dihapus dari `/activities`.
- [x] `/activities` eksklusif menampilkan data `verified`.
- [x] Dashboard merefleksikan hanya data `verified`.
- [x] Peta Rute hanya menampilkan *polyline* dari sesi `verified`.
- [x] Daftar Temuan hanya berisi *event* dari sesi `verified`.
- [x] Menu Sidebar "Verifikasi" mengarah ke `/verifications`.
- [x] Tombol "Verifikasi" (Approve) di tabel berhasil mengubah status menjadi `verified`.
- [x] Tombol "Tolak" (Reject) beserta form alasan berhasil mengubah status menjadi `rejected`.

### UI Test (3/3)
- [x] Halaman Detail Perjalanan bersih dari panel verifikasi (*read-only*).
- [x] Tabel antrian Verifikasi termuat responsif dan rapi.
- [x] Form alasan penolakan (*Alpine.js inline*) muncul mulus tanpa *full-page refresh*.

### Regression Test (1/1 diuji, 1 dilewati)
- [x] Fitur *carousel*/foto di Halaman Detail tidak mengalami *regression*.
- [*UNTESTED*] Uji endpoint `POST /api/sync` — dilewati karena bersifat opsional dan tidak ada sarana Postman tersedia.

### Acceptance Criteria (8/8)
Seluruh 8 kriteria penerimaan dari `iteration.md` terpenuhi.

## Bug Yang Ditemukan
Tidak ada bug fungsional atau teknis yang ditemukan.

## Catatan QA
Tester menemukan 3 masalah desain/UX terkait pengelolaan perjalanan `rejected` yang perlu dibahas:

1. **Tombol aksi masih muncul untuk data `rejected`:** Saat ini, baris data `rejected` di halaman antrian Verifikasi masih menampilkan tombol Approve/Reject secara penuh. Ini membingungkan karena data yang sudah ditolak seharusnya tidak perlu diaksi lagi. Disarankan tombol aksi disembunyikan (atau diganti dengan indikator status saja) untuk baris yang sudah `rejected`.

2. **Tidak ada informasi alasan penolakan yang tersimpan:** Operator menolak dengan mengisi alasan, tetapi alasan tersebut tidak direkam ke database (batasan yang sudah diketahui dari Iterasi 1). Akibatnya, tidak ada cara bagi siapapun untuk mengetahui *mengapa* sebuah perjalanan ditolak. Ini mengurangi nilai audit dan akuntabilitas proses verifikasi.

3. **Risiko status `rejected` ter-reset oleh sinkronisasi mobile:** Jika petugas lapangan melakukan sinkronisasi ulang, `SyncController` akan mereset status sesi kembali ke `submitted` — yang berarti perjalanan yang sudah ditolak dapat "muncul kembali" di antrian dan bahkan di-approve oleh operator lain. Tester mempertanyakan apakah desain ini disengaja, dan menyarankan agar perjalanan yang `rejected` tidak dapat di-approve kembali kecuali melalui mekanisme tertentu.

## Risiko Yang Masih Ada
1. **Alasan Penolakan Tidak Tersimpan:** Belum ada kolom database untuk menyimpan catatan penolakan — perlu menjadi bagian dari iterasi mendatang jika akuntabilitas proses dibutuhkan.
2. **Reset Status oleh Mobile Sync:** Perjalanan `verified`/`rejected` dapat ter-reset ke `submitted` oleh `SyncController` jika petugas melakukan sinkronisasi ulang — risiko mendasar yang belum terselesaikan.
3. **Akses Tanpa Autentikasi:** Halaman `/verifications` tetap dapat diakses oleh siapapun yang mengetahui URL-nya.
