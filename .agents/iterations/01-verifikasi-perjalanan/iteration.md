# ITERATION_01.md

## Verifikasi Perjalanan

---

## 1. Latar Belakang

Aplikasi mobile LILA digunakan oleh petugas lapangan untuk merekam perjalanan patroli. Setiap perjalanan yang selesai dikirim ke server melalui proses sinkronisasi. Setelah sinkronisasi, status perjalanan otomatis diubah menjadi `submitted`.

Saat ini, sistem belum memiliki mekanisme bagi operator monitoring untuk menilai dan memvalidasi data yang masuk. Data dengan status `submitted` diperlakukan sama seperti data yang sudah diperiksa — keduanya tampil di dashboard, daftar perjalanan, dan peta tanpa pembedaan.

Ini berarti data yang mungkin tidak valid, tidak lengkap, atau tidak sesuai protokol patroli tetap terlihat dan dihitung dalam statistik operasional.

---

## 2. Masalah yang Diselesaikan

**Masalah utama:** Tidak ada cara bagi operator untuk menyatakan bahwa sebuah perjalanan sudah diperiksa dan dinyatakan valid atau tidak valid.

**Akibat dari masalah ini:**

- Operator tidak dapat membedakan data yang sudah diperiksa dengan data yang belum diperiksa.
- Tidak ada jejak rekam siapa yang sudah meninjau perjalanan mana.
- Statistik di dashboard mencampur data yang belum ditinjau dengan data yang sudah diverifikasi.
- Tidak ada mekanisme untuk menolak perjalanan yang datanya bermasalah.

---

## 3. Tujuan Iterasi

Menyediakan kemampuan bagi operator monitoring untuk:

1. Melihat daftar perjalanan yang menunggu verifikasi (status `submitted`).
2. Menyatakan sebuah perjalanan sebagai **terverifikasi** (`verified`).
3. Menyatakan sebuah perjalanan sebagai **ditolak** (`rejected`) disertai catatan alasan.
4. Melihat status verifikasi secara jelas pada halaman detail perjalanan.

---

## 4. Ruang Lingkup

### 4.1 Halaman Detail Perjalanan (`/activities/{session}`)

Tambahkan panel aksi verifikasi pada halaman ini.

Panel ditampilkan hanya jika status perjalanan adalah `submitted`.

Panel berisi:
- Tombol **Verifikasi** — untuk mengubah status menjadi `verified`.
- Tombol **Tolak** — untuk mengubah status menjadi `rejected`, disertai input catatan alasan penolakan.

Setelah aksi berhasil:
- Halaman merefleksikan status terbaru (badge status berubah).
- Panel aksi tidak lagi ditampilkan.

### 4.2 Daftar Perjalanan (`/activities`)

Pastikan filter status yang sudah ada tetap berfungsi dengan benar untuk ketiga status: `submitted`, `verified`, `rejected`.

Tidak ada perubahan tampilan lain yang diperlukan pada halaman ini.

### 4.3 Navigasi Sidebar

Ubah item navigasi "Verifikasi (Soon)" yang saat ini dinonaktifkan menjadi tautan aktif yang menuju ke halaman daftar perjalanan dengan filter `status=submitted` sudah aktif.

Ini memungkinkan operator langsung melihat antrian perjalanan yang perlu ditinjau.

---

## 5. Yang Tidak Termasuk Dalam Iterasi Ini

- Autentikasi dan manajemen pengguna — akses ke semua halaman tetap terbuka seperti kondisi saat ini.
- Verifikasi temuan pengamatan (`ActivityEvent`) — ini dijadwalkan di Iterasi 3.
- Perubahan apa pun pada proses sinkronisasi mobile.
- Perubahan pada database schema, migration, atau model.
- Notifikasi atau email kepada petugas lapangan setelah verifikasi.
- Riwayat atau log perubahan status.
- Filter berdasarkan siapa yang memverifikasi.
- Pembatasan akses — siapa pun yang membuka halaman dapat melakukan verifikasi.

---

## 6. Kriteria Selesai

Iterasi dinyatakan selesai apabila seluruh kondisi berikut terpenuhi:

- [ ] Halaman detail perjalanan menampilkan panel verifikasi jika status adalah `submitted`.
- [ ] Operator dapat mengklik tombol **Verifikasi** dan status berubah menjadi `verified`.
- [ ] Operator dapat mengklik tombol **Tolak**, mengisi alasan, dan status berubah menjadi `rejected`.
- [ ] Setelah verifikasi atau penolakan, badge status pada halaman diperbarui.
- [ ] Panel verifikasi tidak muncul jika status sudah `verified` atau `rejected`.
- [ ] Item "Verifikasi" di sidebar menjadi tautan aktif yang menuju ke daftar perjalanan dengan filter `submitted`.
- [ ] Tidak ada perubahan pada database schema, migration, model, atau API mobile.
- [ ] Halaman lain (dashboard, daftar temuan, peta) tidak terpengaruh oleh perubahan ini.

---

## 7. Risiko dan Hal yang Perlu Diperhatikan

### 7.1 Status diperbarui langsung tanpa konfirmasi ganda

Tindakan verifikasi dan penolakan bersifat final (tidak ada undo di iterasi ini). Operator perlu berhati-hati sebelum menekan tombol, terutama tombol Tolak.

Mitigasi: Tambahkan konfirmasi singkat sebelum aksi dieksekusi (misalnya dialog konfirmasi sederhana di browser).

### 7.2 Status dapat direset oleh sinkronisasi ulang mobile

Berdasarkan SYSTEM_ANALYSIS.md, `SyncController::importSession()` selalu mengatur status menjadi `submitted` setiap kali sinkronisasi dilakukan. Artinya, jika petugas lapangan melakukan sinkronisasi ulang untuk perjalanan yang sama, status yang sudah diverifikasi akan kembali ke `submitted`.

Ini adalah keterbatasan sistem yang sudah ada dan **di luar ruang lingkup iterasi ini**. Namun perlu dikomunikasikan kepada pengguna agar tidak mengirim ulang perjalanan yang sudah diverifikasi.

### 7.3 Tidak ada pembatasan akses

Siapa pun yang memiliki akses ke URL dapat melakukan verifikasi karena tidak ada autentikasi. Ini merupakan risiko yang sudah ada sebelum iterasi ini dan tidak diselesaikan di sini.

### 7.4 Alasan penolakan tidak tersimpan di database

Model `TrackingSession` tidak memiliki kolom untuk menyimpan catatan penolakan. Dalam iterasi ini, alasan penolakan hanya berfungsi sebagai langkah konfirmasi bagi operator (memaksa operator menuliskan alasan sebelum menolak), bukan sebagai data yang tersimpan permanen.

Jika kebutuhan menyimpan alasan penolakan muncul di kemudian hari, ini harus dijadikan bagian dari iterasi berikutnya dengan penambahan kolom di database.

---

## 8. Dampak terhadap Pengguna

### Operator Monitoring

**Sebelum iterasi ini:** Operator tidak memiliki cara untuk menandai perjalanan yang sudah diperiksa. Semua perjalanan tampak sama di sistem.

**Setelah iterasi ini:** Operator dapat membuka halaman detail perjalanan dan menyatakan perjalanan tersebut sudah terverifikasi atau ditolak. Tautan "Verifikasi" di sidebar membawa operator langsung ke antrian perjalanan yang belum ditinjau.

### Petugas Lapangan

Tidak ada perubahan pada aplikasi mobile. Proses sinkronisasi tetap sama. Petugas lapangan tidak merasakan perbedaan langsung dari iterasi ini.

### Sistem Secara Keseluruhan

Tidak ada perubahan pada data yang ditampilkan di dashboard, daftar temuan, dan peta. Status verifikasi mulai menjadi informasi yang bermakna, sebagai fondasi untuk Iterasi 2 (Visibility Rule) di mana data terverifikasi akan mulai dibedakan dari data yang belum ditinjau.
