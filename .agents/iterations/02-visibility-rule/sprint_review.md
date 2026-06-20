# Sprint Review

## Ringkasan

Dua iterasi pertama LILA telah diselesaikan dengan status **Completed With Notes**. Sprint ini mencakup:
- **Iterasi 01:** Membangun fondasi proses verifikasi perjalanan.
- **Iterasi 02:** Menerapkan Visibility Rule dan memindahkan verifikasi ke halaman antrian terpusat.

Secara bisnis, sistem telah bertransformasi dari kondisi awal di mana *semua data* tampil tanpa pembedaan, menjadi sistem yang memisahkan data mentah dari data sah. Operator kini memiliki alur kerja yang jelas untuk memeriksa dan memvalidasi data lapangan sebelum data tersebut digunakan dalam analisis dan pelaporan.

---

## Tujuan Yang Tercapai

### Iterasi 01 — Verifikasi Perjalanan
- Operator dapat menyatakan sebuah perjalanan sebagai `verified` atau `rejected`.
- Tautan "Verifikasi" di sidebar aktif dan mengarah langsung ke antrian perjalanan `submitted`.
- Proses verifikasi mewajibkan operator mengisi alasan sebelum menolak.

### Iterasi 02 — Visibility Rule & Sentralisasi Verifikasi
- Dashboard, Peta, Daftar Perjalanan, dan Daftar Temuan **hanya** menampilkan data `verified`.
- Operator dapat memverifikasi banyak perjalanan secara cepat dari satu halaman tabel tanpa pindah-pindah.
- Halaman Detail Perjalanan kembali menjadi halaman pengamatan murni (*read-only*).
- Pemisahan controller: `VerificationController` terpisah dari `ActivityController` (nilai arsitektur jangka panjang).

---

## Tujuan Yang Belum Tercapai

Tidak ada tujuan yang gagal diselesaikan dalam dua iterasi ini. Namun ada **dua kebutuhan baru** yang muncul dari pengujian yang belum direncanakan di roadmap awal:

1. **Penyimpanan alasan penolakan** — Alasan penolakan hanya berfungsi sebagai konfirmasi UI, tidak tersimpan secara permanen.
2. **Pengelolaan status `rejected` yang lebih tegas** — Tidak ada pembatasan aksi untuk data yang sudah ditolak; tombol tetap muncul dan data bisa ter-reset oleh sinkronisasi mobile.

---

## Pembelajaran

1. **Iterasi kecil bekerja dengan baik.** Memecah pekerjaan per masalah bisnis satu-per-satu terbukti menghasilkan implementasi yang bersih, mudah diuji, dan mudah di-review.

2. **Feedback QA mengungkap kebutuhan bisnis yang tidak terpikirkan sebelumnya.** Temuan terkait alasan penolakan dan perilaku data `rejected` adalah kebutuhan yang valid dari sudut pandang akuntabilitas — tetapi baru teridentifikasi saat pengujian nyata.

3. **Risiko reset-by-sync adalah masalah sistemik.** Masalah `SyncController` yang selalu me-reset status ke `submitted` bukan sekadar risiko teknis — ini adalah celah bisnis yang mengancam integritas proses verifikasi. Harus diselesaikan sebelum sistem digunakan secara skala penuh.

4. **Halaman tabular verifikasi adalah UX yang tepat.** Pemindahan fitur dari halaman detail ke halaman antrian terbukti jauh lebih efisien untuk kebutuhan pengecekan massal.

---

## Risiko Yang Masih Terbuka

| Risiko | Tingkat | Keterangan |
|--------|---------|------------|
| Reset status oleh mobile sync | **Tinggi** | Setiap resync dapat membatalkan hasil verifikasi yang sudah dilakukan operator |
| Alasan penolakan tidak tersimpan | **Sedang** | Tidak ada jejak rekam — mengurangi akuntabilitas dan nilai audit |
| Tombol aksi masih muncul untuk `rejected` | **Sedang** | Kebingungan UX: operator dapat secara tidak sengaja meng-approve ulang data yang sudah ditolak |
| Tidak ada autentikasi | **Sedang** | Siapapun dengan URL dapat mengakses `/verifications` dan merubah status data |

---

## Evaluasi Roadmap

Roadmap saat ini:
```
Iteration 1: Verifikasi Perjalanan         ✅ Selesai
Iteration 2: Visibility Rule               ✅ Selesai
Iteration 3: Verifikasi Temuan             📋 Direncanakan
Iteration 4: Heatmap Perjalanan            📋 Direncanakan
Iteration 5: Kategori Temuan              📋 Direncanakan
Iteration 6: Heatmap Temuan               📋 Direncanakan
Iteration 7: Pelaporan dan Statistik       📋 Direncanakan
```

**Evaluasi:**
- Iterasi 3 (Verifikasi Temuan) masih relevan secara bisnis, tetapi ada dua masalah teknis mendasar yang *jika tidak diselesaikan lebih dulu* akan menyebabkan proses verifikasi temuan memiliki cacat yang sama dengan perjalanan.
- Iterasi 4–7 masih relevan dan tidak memerlukan perubahan urutan.
- **Ada kebutuhan baru yang belum ada di roadmap:** perbaikan perilaku `rejected` (UX + logika) dan ketahanan terhadap mobile resync.

---

## Rekomendasi Iterasi Berikutnya

Sebelum melanjutkan ke Iterasi 3 (Verifikasi Temuan), direkomendasikan untuk menyisipkan **Iterasi 2.5 — Perbaikan Alur Rejected** sebagai iterasi minor yang menyelesaikan risiko aktif dari sprint ini:

**Kandidat Pekerjaan Iterasi 2.5:**
1. Sembunyikan tombol aksi untuk baris `rejected` di halaman `/verifications`.
2. Tampilkan informasi alasan penolakan pada baris `rejected` (tanpa perlu menyimpan ke database — cukup dari sesi interaksi atau catatan UI).
3. Tentukan dan dokumentasikan keputusan bisnis: *apakah `rejected` bersifat final, atau dapat di-reset oleh resync mobile?*

Jika keputusan bisnis adalah bahwa `rejected` harus dilindungi dari resync, maka `SyncController` perlu dimodifikasi — ini perlu direncanakan dengan hati-hati karena menyentuh API mobile.

---

## Keputusan

- **Revisi roadmap** — Tambahkan iterasi minor (2.5) untuk menyelesaikan risiko aktif sebelum melanjutkan ke Iterasi 3.
- **Tambah kebutuhan baru** — Dokumentasikan kebutuhan penyimpanan alasan penolakan sebagai backlog untuk dipertimbangkan saat ada keputusan perubahan schema database.
