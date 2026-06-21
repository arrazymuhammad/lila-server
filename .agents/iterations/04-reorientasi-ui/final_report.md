# FINAL REPORT: Iterasi 04
## Reorientasi UI — Observation-Centric

---

## 1. Ringkasan Iterasi

Iterasi 04 berhasil menyelesaikan transformasi visual LILA dari **Tracking-Centric** menjadi **Observation-Centric**. Ini adalah iterasi dengan lingkup terfokus (4 file) namun berdampak besar secara komunikasi produk — setiap metrik yang ditampilkan ke operator kini mencerminkan nilai temuan lapangan, bukan sekadar aktivitas perjalanan.

Lima issue yang diidentifikasi dari UX Review (ISS-001 s/d ISS-005) semuanya diselesaikan dalam satu iterasi ini. Tidak ada bug yang ditemukan selama QA. Tidak ada regresi pada sistem yang ada.

---

## 2. Tujuan Yang Berhasil Dicapai

- [x] **ISS-001** — Grafik tren 7 hari di Dashboard menggunakan jumlah temuan sebagai sumbu Y (bukan jarak km).
- [x] **ISS-002** — Widget Hero Dashboard menampilkan perjalanan paling kaya temuan (bukan perjalanan terjauh).
- [x] **ISS-003** — Summary cards di Dashboard dan Daftar Perjalanan diurutkan: Temuan → Foto → Total Perjalanan → Total Jarak.
- [x] **ISS-004** — Progress bar kartu perjalanan dihitung berdasarkan bobot temuan (×10) dan foto (×5).
- [x] **ISS-005** — Filter "Hanya perjalanan dengan temuan" tersedia dan berfungsi di Daftar Perjalanan.
- [x] Tidak ada perubahan pada API mobile atau kontrak sinkronisasi.
- [x] Halaman-halaman lain (`/map`, `/findings`, `/verifications`) tidak terdampak.

---

## 3. Tujuan Yang Belum Tercapai

Tidak ada. Semua tujuan iterasi tercapai sepenuhnya.

---

## 4. Fitur Yang Ditambahkan

1. **Grafik Tren Temuan** — Dashboard grafik 7 hari kini memvisualisasikan frekuensi temuan per hari.
2. **Widget Hero Observation-Centric** — Secara otomatis menyorot sesi perjalanan dengan temuan verified terbanyak.
3. **Reorder Summary Cards** — Temuan dan Foto selalu tampil di posisi utama di seluruh halaman.
4. **Filter "Hanya dengan Temuan"** — Filter checkbox di `/activities` yang terhubung langsung ke Eloquent `whereHas`.
5. **Progress Bar Berbobot Temuan** — Formula dinamis dengan bobot ×10 untuk temuan dan ×5 untuk foto.

---

## 5. Bug Yang Ditemukan

Tidak ada bug yang ditemukan selama QA.

---

## 6. Deviasi dari Rencana

- Widget Hero sempat menampilkan kolom Temuan dua kali di implementasi awal — sudah diperbaiki menjadi perbandingan murni: Temuan, Jarak, Foto.
- Kalkulasi `maxDensity` untuk progress bar diproses on-the-fly di Blade (`@php`) alih-alih di Controller — keputusan teknis yang valid untuk menghindari beban berlebihan di Controller.

---

## 7. Risiko Yang Masih Terbuka

| # | Risiko | Tingkat | Keterangan |
|---|--------|---------|------------|
| 1 | Grafik/kartu terlihat kosong | Rendah | Kondisi normal selama temuan masih dalam antrian verifikasi |
| 2 | Performa `withCount` harian di skala masif | Sangat Rendah | Tidak relevan di volume data saat ini |
| 3 | Relevansi progress bar jangka panjang | Rendah | Kandidat untuk dievaluasi ulang di iterasi pelaporan |
| 4 | Issue register (ISS-001–005) | ✅ Resolved | Semua issue dalam batch ini selesai |

---

## 8. Catatan untuk Iterasi Berikutnya

1. **Perbarui ISSUES.md** — ISS-001 s/d ISS-005 perlu ditandai sebagai `Resolved`.
2. **Evaluasi Progress Bar** — Pertimbangkan apakah progress bar masih relevan atau perlu diganti dengan indikator yang lebih bermakna di iterasi pelaporan (Iterasi 8).
3. **Iterasi 5 (Heatmap Perjalanan)** — Sudah siap untuk direncanakan. Tidak ada prasyarat yang belum terpenuhi.

---

## 9. Status Iterasi

**Completed — Clean ✅**
Pertama kali dalam roadmap ini sebuah iterasi selesai tanpa catatan bug maupun item yang belum tercapai.
