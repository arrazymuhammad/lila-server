
# ROADMAP.md

## Visi Produk

LILA bukan sekadar aplikasi tracking GPS.

LILA adalah sistem monitoring, verifikasi, dan analisis temuan lapangan yang berasal dari aktivitas patroli dan pengamatan.

Tracking berfungsi sebagai sumber data.

Nilai utama sistem adalah:

* Perjalanan
* Temuan Pengamatan
* Bukti Foto
* Lokasi Temuan
* Proses Verifikasi

---

## Kondisi Saat Ini

Berdasarkan SYSTEM_ANALYSIS.md dan iterasi yang sudah selesai:

Modul yang tersedia:

* Dashboard
* Perjalanan (dengan Visibility Rule — hanya verified)
* Temuan Pengamatan (dengan Visibility Rule — hanya verified)
* Peta (dengan Visibility Rule — hanya verified)
* Verifikasi Perjalanan (`/verifications`)

Status data yang tersedia:

* `tracking_sessions`: submitted, verified, rejected
* `activity_events`: submitted, verified, rejected (kolom ditambahkan manual — Iterasi 03)

Proses verifikasi dua tingkat (perjalanan + temuan) sudah tersedia dan aktif. Seluruh data yang tampil di publik sudah melewati proses verifikasi operator.

---

## Tujuan Jangka Pendek

1. Memastikan kualitas data sebelum digunakan untuk analisis.
2. Memisahkan data mentah dan data terverifikasi.
3. Menyediakan proses verifikasi yang sederhana.
4. Menjadikan data terverifikasi sebagai sumber utama dashboard dan peta.

---

## Roadmap

### Iteration 1 ✅

Verifikasi Perjalanan

Status: Completed With Notes

### Iteration 2 ✅

Visibility Rule & Sentralisasi Verifikasi

Status: Completed With Notes

### Iteration 3-A ✅

Verifikasi Temuan Pengamatan (Inti)

Cakupan: Antrian verifikasi temuan, detail verifikasi individual (foto, peta, kategori), SyncController update, Visibility Rule temuan, toggle peta.

Status: Completed With Notes

### Iteration 3-B ✅

Pengayaan Kategori Temuan oleh Operator

Cakupan: Operator dapat mengubah atau memberikan kategori pada temuan saat atau setelah verifikasi. Data `operator_category` tersimpan terpisah dari kategori asli mobile dengan fitur auto-suggest.

Status: Completed With Notes

### Iteration 4 📋

Reorientasi UI — Observation-Centric

Cakupan: Mengubah prioritas visual Dashboard dan Daftar Perjalanan agar selaras dengan visi Observation-Centric. Termasuk: reorder summary cards, grafik tren berbasis temuan (bukan jarak), formula progress bar berbobot temuan, toggle filter perjalanan dengan temuan, dan optimalisasi sorting.

Dasar: Issue ISS-001 s/d ISS-005 dari UX Review (issues/raw/001.md)

### Iteration 5 📋

Heatmap Perjalanan

### Iteration 6 📋

Manajemen Kategori Master

Cakupan: Buat tabel master kategori temuan, antarmuka CRUD untuk operator, dan hubungkan dengan field `operator_category` yang sudah ada.

Catatan: Menggantikan "Kategori Temuan" lama — sebagian sudah diselesaikan di Iterasi 03-B.

### Iteration 7 📋

Heatmap Temuan

### Iteration 8 📋

Pelaporan dan Statistik Lanjutan

---

## Prinsip Pengembangan

* Perubahan dilakukan secara bertahap.
* Satu iterasi fokus pada satu masalah bisnis.
* Hindari refactor besar.
* Utamakan perubahan yang memberikan nilai bisnis tertinggi.
* Kompatibilitas mobile tetap dipertahankan.
