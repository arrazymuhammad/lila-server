# ITERATION_03.md (Revised)

## Verifikasi Temuan Pengamatan

> Revisi berdasarkan keputusan Product Owner pasca Pre-Implementation Review.

---

## Keputusan yang Telah Ditetapkan

| # | Topik | Keputusan |
|---|-------|-----------|
| 1 | Kolom `status` di `activity_events` | Ditambahkan secara manual oleh admin database |
| 2 | Modifikasi `SyncController` | **Diizinkan dan wajib** — events baru default `submitted` |
| 3 | Status data temuan lama | Semua menjadi `submitted` (harus diverifikasi ulang) |
| 4 | Mekanisme verifikasi | Satu per satu — operator harus melihat foto, lokasi, dan kategori |
| 5 | Cakupan verifikasi | Pengecekan mendalam: koordinat, kategori, deskripsi, foto |
| 6 | Operator dapat ubah kategori | Ya — sebagai bagian dari proses verifikasi |
| 7 | Visibility Rule peta | Strict dengan toggle di UI halaman peta (per-session, tanpa database config) |

---

## Rekomendasi Pemecahan Scope

Berdasarkan analisis teknis dan keputusan di atas, Iterasi 03 direkomendasikan dipecah menjadi dua:

### Iterasi 03-A: Verifikasi Temuan (Inti)
Fokus pada alur verifikasi — meninjau dan menyetujui/menolak temuan.

### Iterasi 03-B: Pengayaan Kategori Temuan
Fokus pada kemampuan operator mengubah/memberikan kategori saat atau setelah verifikasi.

> **Keputusan untuk Product Owner:** Apakah Anda setuju dengan pemecahan ini, atau ingin mengerjakan keduanya dalam satu iterasi?

---

## 1. Latar Belakang

Iterasi 03-A membangun di atas fondasi yang sudah ada dari Iterasi 01-02. Proses verifikasi perjalanan sudah berjalan. Langkah berikutnya adalah memastikan setiap **Temuan Pengamatan** (`ActivityEvent`) dalam perjalanan yang terverifikasi juga telah dikonfirmasi kualitasnya oleh operator sebelum tampil di sistem analisis.

---

## 2. Masalah yang Diselesaikan

- Temuan dari perjalanan yang sudah `verified` belum tentu akurat secara individual — koordinat bisa drift, kategori bisa salah pilih, foto bisa tidak relevan.
- Tidak ada mekanisme bagi operator untuk menyatakan bahwa sebuah temuan sudah diperiksa.
- Daftar Temuan dan Peta belum membedakan temuan yang sudah dikonfirmasi dengan yang baru masuk.

---

## 3. Tujuan Iterasi (03-A)

1. Menyediakan antrian verifikasi temuan yang dapat dinavigasi satu per satu.
2. Pada setiap temuan dalam antrian, operator dapat melihat: detail temuan, foto, koordinat di peta mini, dan kategori.
3. Operator dapat menyetujui (`verified`) atau menolak (`rejected`) setiap temuan.
4. Daftar Temuan (`/findings`) hanya menampilkan temuan `verified`.
5. Peta menampilkan marker temuan `verified` saja, dengan toggle di UI untuk melihat semua (termasuk `submitted`).

---

## 4. Ruang Lingkup

### 4.1 Halaman Antrian Verifikasi Temuan

- Route baru: `/verifications/findings`
- Tampilkan daftar temuan berstatus `submitted` dari perjalanan yang `verified`
- Setiap baris menampilkan: nama perjalanan, judul/kategori temuan, waktu, jumlah foto
- Klik baris → buka halaman detail verifikasi temuan individual

### 4.2 Halaman Detail Verifikasi Temuan Individual

- Route baru: `/verifications/findings/{event}`
- Tampilkan secara lengkap:
  - Peta titik koordinat temuan
  - Galeri foto temuan
  - Kategori (tampilkan label kategori, bukan angka ID)
  - Deskripsi
  - Informasi perjalanan induk
- Tombol: **Verifikasi** dan **Tolak**
- Navigasi: tombol "Temuan Berikutnya" dan "Temuan Sebelumnya" dalam satu perjalanan

### 4.3 Pembaruan `SyncController`

- Saat `importEvents()`, set `status = 'submitted'` untuk setiap event yang baru dibuat
- Untuk event yang sudah ada (`updateOrCreate`), **jangan** override status yang sudah ada (preserve status `verified` jika sudah diverifikasi)

### 4.4 Visibility Rule — `FindingController`

- Daftar Temuan (`/findings`) hanya query `ActivityEvent` berstatus `verified`

### 4.5 Visibility Rule — `MapController` dengan Toggle

- Default: hanya tampilkan marker temuan `verified`
- Tambahkan toggle switch di UI halaman peta: "Tampilkan semua temuan" / "Hanya temuan terverifikasi"
- State toggle disimpan di `localStorage` browser (tidak perlu database)
- Marker `submitted` tampil dengan warna berbeda (abu-abu) saat toggle aktif

### 4.6 Navigasi Sidebar

- Tambahkan sub-item "Temuan" di bawah menu "Verifikasi" yang sudah ada
- Tampilkan badge jumlah temuan `submitted` yang menunggu verifikasi

---

## 5. Yang Tidak Termasuk Dalam Iterasi Ini

- **Pengubahan kategori oleh operator** — dijadwalkan di Iterasi 03-B / Iterasi 05
- Verifikasi foto (`ActivityPhoto`) secara terpisah
- Bulk approve/reject multiple temuan sekaligus
- Penyimpanan catatan alasan penolakan temuan secara permanen (mengikuti BL-001)
- Autentikasi pengguna

---

## 6. Kriteria Selesai

- [ ] Kolom `status` tersedia di tabel `activity_events` (ditambahkan manual oleh admin)
- [ ] `SyncController` mengisi `status = 'submitted'` untuk events baru, tanpa override yang sudah `verified`
- [ ] Terdapat halaman antrian `/verifications/findings` yang menampilkan temuan `submitted`
- [ ] Operator dapat membuka detail temuan individual dan melakukan Approve/Reject
- [ ] Halaman detail verifikasi temuan menampilkan foto, peta koordinat, dan kategori
- [ ] Ada navigasi "Temuan Berikutnya/Sebelumnya" di halaman detail verifikasi
- [ ] `/findings` hanya menampilkan temuan `verified`
- [ ] Peta memiliki toggle: strict (hanya verified) vs all (semua temuan)
- [ ] Sidebar menampilkan sub-menu "Temuan" di bawah "Verifikasi"
- [ ] Tidak ada perubahan pada API mobile atau kontrak sinkronisasi

---

## 7. Risiko dan Hal yang Perlu Diperhatikan

### 7.1 Antrian Sangat Panjang di Awal
Semua temuan lama akan berstatus `submitted`. Dengan puluhan temuan per perjalanan dan banyak perjalanan yang sudah `verified`, antrian awal bisa sangat panjang. Fitur paginasi dan filter yang baik di halaman antrian sangat penting.

### 7.2 SyncController — Override Protection
Logika `updateOrCreate` di SyncController harus hati-hati: jika event sudah `verified`, resync tidak boleh mengubah statusnya kembali ke `submitted`. Implementasi perlu menggunakan pendekatan "update hanya kolom non-status" atau "skip status update jika sudah verified".

### 7.3 Kolom Tambahan di `activity_events` (untuk 03-B)
Jika Iterasi 03-B ingin menyimpan kategori hasil edit operator, diperlukan kolom tambahan (misal `operator_category`). Ini **tidak** termasuk dalam iterasi ini, namun perlu didiskusikan agar kolom bisa ditambahkan sekalian saat admin menambah kolom `status`.

---

## 8. Dampak Terhadap Pengguna

### Operator Monitoring
Mendapat antrian verifikasi temuan yang terstruktur. Dapat meninjau setiap temuan secara mendalam sebelum data digunakan dalam analisis.

### Pengguna Peta
Peta menjadi lebih akurat — hanya menampilkan temuan yang sudah dikonfirmasi. Dengan toggle, operator masih bisa melihat semua temuan untuk keperluan monitoring antrian.

### Petugas Lapangan
Tidak ada perubahan pada aplikasi mobile.
