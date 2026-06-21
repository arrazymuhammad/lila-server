# QA Report
## Iterasi 04 — Reorientasi UI Observation-Centric

**Status: PASS ✅**

---

## Ringkasan Pengujian

Seluruh item dalam QA Checklist Iterasi 04 telah diuji dan lulus. Semua 7 acceptance criteria dari ISS-001 hingga ISS-005 terpenuhi. Tidak ada bug yang ditemukan. Tidak ada regresi pada halaman-halaman yang tidak diubah.

---

## Hasil Checklist

| Area | Hasil |
|------|-------|
| Dashboard — Grafik Tren 7 Hari | ✅ 4/4 Lulus |
| Dashboard — Widget Hero | ✅ 3/3 Lulus |
| Dashboard — Summary Cards | ✅ 3/3 Lulus |
| Activities — Summary Cards | ✅ 3/3 Lulus |
| Activities — Filter Temuan | ✅ 6/6 Lulus |
| Activities — Sorting Temuan | ✅ 3/3 Lulus |
| Activities — Progress Bar | ✅ 3/3 Lulus |
| UI Test | ✅ 7/7 Lulus |
| Regression Test | ✅ 12/12 Lulus |
| Acceptance Criteria | ✅ 8/8 Lulus |

---

## Bug Yang Ditemukan

**Tidak ada bug yang ditemukan.**

---

## Catatan QA

### 1. Grafik Kosong adalah Kondisi yang Diharapkan
Jika belum ada temuan `verified` dalam 7 hari terakhir, grafik tren menampilkan semua batang bernilai nol tanpa error. Ini sesuai dengan Risiko 7.1 yang sudah didokumentasikan di `iteration.md`.

### 2. Progress Bar — Catatan untuk Roadmap
Tester mencatat bahwa progress bar kartu perjalanan kemungkinan perlu dievaluasi ulang secara keseluruhan di iterasi berikutnya. Mengingat metrik utama sudah bergeser ke temuan, relevansi progress bar berbasis kombinasi track point + temuan + foto masih bisa diperdebatkan. Disarankan untuk dimasukkan sebagai item backlog untuk dipertimbangkan saat iterasi pelaporan atau analisis.

---

## Risiko Yang Masih Ada

| # | Risiko | Tingkat | Keterangan |
|---|--------|---------|------------|
| 1 | Grafik temuan terlihat datar/kosong di awal | Rendah | Kondisi normal selama antrian temuan belum diverifikasi tuntas |
| 2 | Relevansi progress bar jangka panjang | Rendah | Perlu evaluasi ulang di iterasi pelaporan |
| 3 | Risiko-risiko dari iterasi sebelumnya | — | Tidak ada perubahan dari QA Report Iterasi 03 |

---

## Issue Yang Diselesaikan

| Issue | Deskripsi | Status |
|-------|-----------|--------|
| ISS-001 | Grafik tren mengukur Jarak | ✅ Resolved |
| ISS-002 | Widget Hero = Perjalanan Terjauh | ✅ Resolved |
| ISS-003 | Summary cards menonjolkan tracking | ✅ Resolved |
| ISS-004 | Progress bar berbasis track point | ✅ Resolved |
| ISS-005 | Tidak ada filter perjalanan dengan temuan | ✅ Resolved |
