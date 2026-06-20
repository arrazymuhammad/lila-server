
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

Proses verifikasi perjalanan sudah tersedia. Verifikasi temuan sedang dalam pengerjaan.

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

### Iteration 3-A 🔄

Verifikasi Temuan Pengamatan (Inti)

Cakupan: Antrian verifikasi temuan, detail verifikasi individual (foto, peta, kategori), SyncController update, Visibility Rule temuan, toggle peta.

Status: In Progress

### Iteration 3-B 📋

Pengayaan Kategori Temuan oleh Operator

Cakupan: Operator dapat mengubah atau memberikan kategori pada temuan saat atau setelah verifikasi. Data kategori operator disimpan terpisah dari kategori asli mobile.

Catatan: Tumpang tindih dengan Iteration 5. Akan dievaluasi saat 3-A selesai apakah 3-B dan 5 perlu digabung.

Status: Planned

### Iteration 4 📋

Heatmap Perjalanan

### Iteration 5 📋

Kategori Temuan

Catatan: Kemungkinan digabung atau disederhanakan setelah Iteration 3-B selesai.

### Iteration 6 📋

Heatmap Temuan

### Iteration 7 📋

Pelaporan dan Statistik Lanjutan

---

## Prinsip Pengembangan

* Perubahan dilakukan secara bertahap.
* Satu iterasi fokus pada satu masalah bisnis.
* Hindari refactor besar.
* Utamakan perubahan yang memberikan nilai bisnis tertinggi.
* Kompatibilitas mobile tetap dipertahankan.
