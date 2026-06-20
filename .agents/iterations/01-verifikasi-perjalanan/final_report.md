# FINAL REPORT: Iterasi 01 - Verifikasi Perjalanan

## 1. Ringkasan Iterasi
Iterasi 01 berfokus pada implementasi mekanisme verifikasi perjalanan, yang memungkinkan operator monitoring untuk meninjau data dari petugas lapangan yang berstatus `submitted`. Operator dibekali hak untuk menyetujui (`verified`) atau menolak (`rejected`) data tersebut. Fase implementasi telah dieksekusi dengan baik, diikuti dengan fase Quality Assurance (QA) yang memberikan tingkat kelulusan fungsionalitas dan uji regresi 100%. Meski berhasil memenuhi kriteria iterasi, *feedback* QA menggarisbawahi perlunya penyempurnaan di sisi alur UX agar pengoperasian secara masal jauh lebih efisien.

## 2. Tujuan Yang Berhasil Dicapai
- [x] Mengaktifkan panel aksi di halaman detail perjalanan ketika status masih `submitted`.
- [x] Menyediakan fungsi verifikasi untuk merubah status langsung menjadi `verified`.
- [x] Menyediakan fungsi penolakan yang mewajibkan input alasan sebelum merubah status menjadi `rejected`.
- [x] Memperbarui *badge* status pada tampilan secara instan setelah data diproses.
- [x] Mengaktifkan tautan "Verifikasi" pada sidebar yang mengarah langsung ke daftar `submitted`.
- [x] Mengimplementasikan seluruh logika dan UI *tanpa* melakukan perubahan apa pun pada database schema maupun API mobile.

## 3. Tujuan Yang Belum Tercapai
Tidak ada objektif (dari *iteration.md*) yang terlewat. Namun dari segi ergonomi antarmuka pengguna, penempatan fitur verifikasi secara langsung pada halaman Detail dipandang kurang efisien bila dikomparasikan dengan layout berbasis *tabel/tabular khusus*.

## 4. Fitur Yang Ditambahkan
1. **Modul Verifikasi Perjalanan:** Terletak di halaman Detail Perjalanan `/activities/{session}` beserta popup form berbasis *Alpine.js*.
2. **Endpoint Pemrosesan Status:** Endpoint `PATCH` mandiri di `ActivityController@verify`.
3. **Aktivasi Pintasan Sidebar:** Menu verifikasi kini otomatis menyaring antrian pada halaman `/activities?status=submitted`.

## 5. Bug Yang Ditemukan
Tidak ada *bug* (baik minor maupun mayor/kritis) yang dijumpai sepanjang tahapan pengujian fitur terkait di lingkungan lokal.

## 6. Risiko Yang Masih Terbuka
- **Tertimpa Fitur Sinkronisasi:** Keterbatasan pada `/api/sync` menyebabkan data yang sudah disetujui atau ditolak bisa tanpa sengaja tersetel ulang menjadi `submitted` ketika petugas lapangan melakukan *sync* ulang. 
- **Hilangnya Rekam Jejak Penolakan:** Karena alasan "Tolak" tidak direkam secara tetap ke dalam *database*, informasi tersebut tidak akan terbaca di kemudian hari.
- **Keamanan Aplikasi:** Fitur ini dapat diakses oleh siapapun yang memiliki rute alamat URL-nya akibat belum adanya sistem autentikasi di dalam aplikasi.

## 7. Catatan Untuk Iterasi Berikutnya
Sesuai masukan krusial dari tim Tester/QA, poin-poin *improvement* ini diharapkan menjadi *backlog* prioritas di masa depan (bisa jadi untuk Iterasi 2 `Visibility Rule` atau iterasi minor penyempurnaan UI):
1. **Ekstraksi Modul Verifikasi:** Pisahkan URL panel Verifikasi dari halaman detail. Buat halaman baru berbasis daftar/tabel (*tabular*) yang difokuskan khusus bagi tugas verifikasi agar proses pengecekan masal lebih mudah diakses.
2. **Read-only Mode pada Detail:** Kembalikan Halaman Detail Perjalanan sebagai layar pengamatan konvensional yang tidak memiliki izin interaksi edit data (*Read-Only*).
3. **Filter Eksklusif:** Rancang Daftar Perjalanan Utama (`/activities`) agar khusus menampilkan dan merangkum yang sudah berstatus `verified` saja.

## 8. Status Iterasi
**Completed With Notes**
