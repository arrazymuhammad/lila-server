# Handoff Prompt: Pipeline Pra-Iterasi 5
## LILA Web Application — Proyek Monitoring Lapangan

---

## Konteks Proyek

Kamu melanjutkan sesi sebelumnya pada proyek **LILA** — sebuah sistem monitoring, verifikasi, dan analisis temuan lapangan berbasis Laravel. Lokasi kode: `d:\laragon\www\lila`. Semua dokumen perencanaan berada di `d:\laragon\www\lila\.agents\`.

---

## Status Terakhir

Iterasi 04 (Reorientasi UI Observation-Centric) telah **selesai sepenuhnya** — semua dokumen iterasi sudah dibuat:

- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\iteration.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\review.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\implementation_report.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\qa_checklist.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\qa_report.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\final_report.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\sprint_review.md`

ISS-001 s/d ISS-005 sudah ditandai **Resolved** di `ISSUES.md`.

---

## Tugas Yang Harus Kamu Lakukan

Jalankan **pipeline pra-Iterasi 5** secara berurutan:

### Langkah 1 — Roadmap Review

Baca dokumen-dokumen berikut:
- `d:\laragon\www\lila\.agents\AI_CONTEXT.md`
- `d:\laragon\www\lila\.agents\ROADMAP.md`
- `d:\laragon\www\lila\.agents\BACKLOG.md`
- `d:\laragon\www\lila\.agents\issues\ISSUES.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\sprint_review.md`
- `d:\laragon\www\lila\.agents\iterations\04-reorientasi-ui\final_report.md`
- Roadmap review historis terbaru: `d:\laragon\www\lila\.agents\roadmap_review\roadmap_review_002.md`

Buat file **baru** (JANGAN timpa yang lama):
`d:\laragon\www\lila\.agents\roadmap_review\roadmap_review_003.md`

Evaluasi:
1. Apakah urutan iterasi berikutnya masih tepat?
2. Apakah ada item backlog yang lebih mendesak dari Iterasi 5?
3. Apakah ada risiko aktif yang mengubah prioritas?

---

### Langkah 2 — Backlog Review

Baca:
- `d:\laragon\www\lila\.agents\BACKLOG.md`
- `d:\laragon\www\lila\.agents\issues\ISSUES.md`
- `roadmap_review_003.md` yang baru dibuat

Buat file (timpa yang lama):
`d:\laragon\www\lila\.agents\backlog_review.md`

Evaluasi:
1. Item mana yang naik/turun prioritas?
2. Apakah ada item yang perlu dipromosikan ke roadmap?
3. Apakah ada item baru dari catatan iterasi 04 yang perlu ditambahkan?

---

### Langkah 3 — Backlog & Roadmap Maintenance

Baca:
- `d:\laragon\www\lila\.agents\roadmap_review\roadmap_review_003.md`
- `d:\laragon\www\lila\.agents\backlog_review.md`
- `d:\laragon\www\lila\.agents\ROADMAP.md`
- `d:\laragon\www\lila\.agents\BACKLOG.md`

Terapkan semua keputusan dari roadmap_review dan backlog_review ke:
- `d:\laragon\www\lila\.agents\ROADMAP.md` — perbarui status Iterasi 4 menjadi Completed, konfirmasi Iterasi 5 sebagai berikutnya
- `d:\laragon\www\lila\.agents\BACKLOG.md` — tambah/update item sesuai keputusan review

**Larangan:**
- Jangan membuat keputusan baru yang tidak ada dasar di dokumen review
- Jangan mengubah prioritas tanpa dasar

---

### Langkah 4 — Persiapan Iterasi 5 (iteration.md)

Iterasi 5 di roadmap adalah: **Heatmap Perjalanan**

Sebelum membuat `iteration.md`, lakukan audit kode aktual pada:
- `d:\laragon\www\lila\app\Http\Controllers\MapController.php`
- `d:\laragon\www\lila\resources\views\maps\index.blade.php`
- `d:\laragon\www\lila\routes\web.php` (cari route `/map` dan turunannya)
- Cek apakah ada library heatmap yang sudah terpasang di `resources/js` atau package.json

Buat file:
`d:\laragon\www\lila\.agents\iterations\05-heatmap-perjalanan\iteration.md`

Isi iteration.md harus mencakup:
1. Pertimbangan Backlog vs Roadmap
2. Latar Belakang (berdasarkan kondisi kode aktual)
3. Masalah yang Diselesaikan
4. Tujuan Iterasi
5. Ruang Lingkup (dengan referensi ke file dan fungsi spesifik)
6. Yang Tidak Termasuk
7. Kriteria Selesai
8. Risiko dan Hal yang Perlu Diperhatikan
9. Dampak Terhadap Pengguna

---

### Langkah 5 — Pre-Implementation Review (review.md)

Setelah `iteration.md` selesai, lakukan technical review.

Buat file:
`d:\laragon\www\lila\.agents\iterations\05-heatmap-perjalanan\review.md`

Review harus mencakup:
1. Apakah ruang lingkup sudah jelas?
2. Apakah ada konflik dengan sistem saat ini?
3. Apakah ada library yang perlu ditambahkan (leaflet heatmap, dll)?
4. Apakah ada risiko yang belum disebutkan?
5. Estimasi file yang berubah
6. Urutan implementasi yang disarankan
7. Status akhir: CLEAR / NEEDS CLARIFICATION

---

## Aturan Penting

1. **Baca kode aktual** sebelum membuat iteration.md dan review.md — jangan berasumsi tentang kondisi kode.
2. **Jangan ubah API mobile** — kompatibilitas mobile adalah prioritas mutlak.
3. **Jangan buat migration database** tanpa persetujuan eksplisit — tambahan kolom dilakukan manual oleh admin.
4. **Jangan timpa file historis** (roadmap_review_001, roadmap_review_002) — selalu buat nomor baru.
5. **Setiap keputusan** yang kamu buat harus memiliki dasar dari dokumen yang kamu baca.

---

## Konteks Penting yang Perlu Diketahui

**Struktur `.agents/`:**
```
.agents/
├── AI_CONTEXT.md          ← Visi produk & constraint
├── SYSTEM_ANALYSIS.md     ← Audit sistem teknis lengkap
├── ROADMAP.md             ← Roadmap aktif
├── BACKLOG.md             ← Backlog aktif
├── backlog_review.md      ← Review backlog terbaru (bisa ditimpa)
├── issues/
│   ├── ISSUES.md          ← Issue register (ISS-001–005 sudah Resolved)
│   ├── raw/               ← Raw feedback mentah
│   └── analysis/          ← Hasil analisis issue
├── roadmap_review/
│   ├── roadmap_review_001.md  ← Review pasca Iterasi 01-02
│   ├── roadmap_review_002.md  ← Review pasca Iterasi 03-04 ← TERBARU
│   └── roadmap_review_003.md  ← Yang harus kamu buat
├── iterations/
│   ├── 01-verifikasi-perjalanan/
│   ├── 02-visibility-rule/
│   ├── 03-verifikasi-temuan/
│   ├── 04-reorientasi-ui/    ← Baru selesai
│   └── 05-heatmap-perjalanan/ ← Yang harus kamu siapkan
└── templates/             ← Template prompt untuk tiap fase
```

**Roadmap Saat Ini:**
```
✅ Iteration 1:   Verifikasi Perjalanan
✅ Iteration 2:   Visibility Rule
✅ Iteration 3-A: Verifikasi Temuan (Inti)
✅ Iteration 3-B: Pengayaan Kategori Temuan
✅ Iteration 4:   Reorientasi UI Observation-Centric
🔜 Iteration 5:   Heatmap Perjalanan   ← TARGET
📋 Iteration 6:   Manajemen Kategori Master
📋 Iteration 7:   Heatmap Temuan
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan
```

**Backlog penting yang perlu kamu pantau:**
- BL-009 (Manajemen Kategori Master) — High, ketergantungan schema belum diputuskan
- BL-007 (Edit Mode Temuan Verified) — Medium
- BL-001 (Rejected Reason Persistence) — High, menunggu keputusan migration
