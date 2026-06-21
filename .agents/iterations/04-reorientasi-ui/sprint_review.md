# Sprint Review
## Iterasi 04 — Reorientasi UI Observation-Centric

> Tanggal: 2026-06-21
> Status: **Completed — Clean ✅**

---

## Ringkasan

Iterasi 04 adalah iterasi paling bersih sejauh ini — selesai tanpa bug, tanpa item yang gagal, dan tanpa regresi. Dengan hanya mengubah 4 file, LILA telah menyelesaikan transformasi visual yang fundamental: dari aplikasi yang merayakan kilometer perjalanan, menjadi sistem yang menonjolkan temuan pengamatan lapangan sebagai pencapaian utama.

Ini adalah milestone penting dalam roadmap — **visi Observation-Centric kini konsisten dari backend hingga frontend**.

---

## Tujuan Yang Tercapai

- [x] Grafik tren 7 hari menampilkan jumlah temuan per hari — bukan jarak.
- [x] Widget Hero menyoroti perjalanan paling kaya temuan — bukan perjalanan terjauh.
- [x] Summary cards diurutkan ulang: Temuan dan Foto selalu di posisi utama.
- [x] Progress bar kartu perjalanan mencerminkan nilai pengamatan (bobot temuan ×10, foto ×5).
- [x] Filter "Hanya perjalanan dengan temuan" tersedia dan berfungsi.
- [x] Semua issue dari UX Review (ISS-001 s/d ISS-005) diselesaikan dalam satu iterasi.

---

## Tujuan Yang Belum Tercapai

Tidak ada. Iterasi ini selesai 100%.

---

## Pembelajaran

1. **Issue yang terdokumentasi dengan baik mempercepat implementasi.** Karena ISS-001 s/d ISS-005 sudah disertai action items teknis yang konkret di `issue_analysis_001.md`, implementasi dapat berjalan langsung tanpa banyak interpretasi.

2. **Perubahan UI murni sangat aman.** Iterasi yang hanya menyentuh layer view dan query order (tanpa mengubah schema atau API) menghasilkan scope yang terkontrol ketat — QA pun berjalan sangat cepat.

3. **Pipeline issue → analysis → roadmap terbukti efektif.** Feedback UX Review yang masuk sebagai raw feedback (`issues/raw/001.md`) berhasil diproses menjadi iterasi yang terstruktur dan terselesaikan dalam satu siklus. Ini memvalidasi pipeline baru yang sudah dibangun.

---

## Risiko Yang Masih Terbuka

| # | Risiko | Tingkat |
|---|--------|---------|
| 1 | Grafik/kartu terlihat kosong selama antrian verifikasi temuan belum tuntas | Rendah |
| 2 | Relevansi progress bar jangka panjang — perlu dievaluasi ulang | Rendah |
| 3 | BL-001 s/d BL-002 dari backlog — belum ada keputusan | Sedang |
| 4 | BL-009 Manajemen Kategori Master — semakin mendesak seiring bertambahnya data | Sedang |

---

## Evaluasi Roadmap

```
✅ Iteration 1:   Verifikasi Perjalanan              [SELESAI]
✅ Iteration 2:   Visibility Rule                    [SELESAI]
✅ Iteration 3-A: Verifikasi Temuan (Inti)           [SELESAI]
✅ Iteration 3-B: Pengayaan Kategori Temuan          [SELESAI]
✅ Iteration 4:   Reorientasi UI Observation-Centric [SELESAI — CLEAN]
📋 Iteration 5:   Heatmap Perjalanan                 [BERIKUTNYA]
📋 Iteration 6:   Manajemen Kategori Master          [DIRENCANAKAN]
📋 Iteration 7:   Heatmap Temuan                     [DIRENCANAKAN]
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan   [DIRENCANAKAN]
```

Roadmap tetap relevan. Iterasi 5 (Heatmap Perjalanan) tidak memiliki prasyarat yang belum terpenuhi dan siap direncanakan.

---

## Rekomendasi Iterasi Berikutnya

**Lanjut ke Iterasi 5 — Heatmap Perjalanan** sesuai roadmap.

Sebelum memulai, perlu dilakukan:
1. Perbarui `ISSUES.md` — tandai ISS-001 s/d ISS-005 sebagai Resolved.
2. Jalankan pipeline standar: roadmap review → backlog review → maintenance.

---

## Keputusan

- **Lanjut roadmap** — Iterasi 5 (Heatmap Perjalanan) menjadi prioritas berikutnya.
- **Update ISSUES.md** — Tandai ISS-001 s/d ISS-005 sebagai Resolved sebelum sesi berikutnya.
