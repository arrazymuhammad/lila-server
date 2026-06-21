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
5. Membangun pondasi keamanan dengan memproteksi halaman dashboard dan memfasilitasi autentikasi API mobile.

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

Status: Completed With Notes

### Iteration 3-B ✅

Pengayaan Kategori Temuan oleh Operator

Status: Completed With Notes

### Iteration 4 ✅

Reorientasi UI — Observation-Centric

Status: Completed ✅

### Iteration 5 ✅

Heatmap Perjalanan

Status: Completed ✅

### Iteration 6 ✅

Manajemen Kategori Master

Status: Completed ✅

### Iteration 7 🔜

Heatmap Temuan Berdasarkan Kategori

Status: Target (In Preparation)

### Iteration 8 ✅

Peta Interaktif: Rich Finding Popup

Status: Completed ✅

### Iteration 9 ✅

Autentikasi Admin & Proteksi Dashboard

Status: Completed ✅

Cakupan: Mengamankan sistem dengan membuat fitur login web. Menambahkan middleware `auth` ke seluruh rute dashboard, peta, dan verifikasi. Halaman web ini bersifat tertutup (hanya untuk operator/admin) sehingga tidak menyediakan akses registrasi publik. Mengalihkan halaman login menjadi gerbang utama. Akun pertama dibuat melalui eksekusi manual Seeder/Tinker.

### Iteration 10 📋

Otentikasi API Mobile (Sanctum)

Cakupan: Instalasi dan konfigurasi Laravel Sanctum untuk aplikasi mobile. Menyediakan endpoint API JSON murni untuk `/api/register` dan `/api/login` yang akan mengeluarkan token (bearer) khusus perangkat mobile.

### Iteration 11 📋

Relasi Data Pelapor pada Sinkronisasi

Cakupan: Menambahkan kolom `user_id` pada tabel `tracking_sessions` dan entitas terkait. Mengubah endpoint `/api/sync` agar menggunakan middleware `auth:sanctum` dan mendata identitas pelapor berdasarkan token mobile saat proses sinkronisasi data berlangsung.

### Iteration 12 📋

Pelaporan dan Statistik Lanjutan

Cakupan: Eksport data, rekapitulasi data bulanan/tahunan (PDF/Excel) untuk laporan manajerial dari data yang telah tervalidasi.

---

## Prinsip Pengembangan

* Perubahan dilakukan secara bertahap.
* Satu iterasi fokus pada satu masalah bisnis.
* Hindari refactor besar.
* Utamakan perubahan yang memberikan nilai bisnis tertinggi.
* Kompatibilitas mobile tetap dipertahankan.
