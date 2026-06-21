# ISSUES.md
## Issue Register — LILA Web Application

> Dokumen ini adalah register resmi semua issue yang teridentifikasi dari feedback pengguna, QA, dan review internal.
> Issue dapat dipromosikan ke BACKLOG atau langsung ke Roadmap melalui proses review.

---

## STATUS LEGEND

| Status | Keterangan |
|--------|-----------|
| Open | Baru terdaftar, belum ada keputusan |
| Confirmed | Dikonfirmasi valid dan perlu ditangani |
| In Backlog | Sudah dipindahkan ke BACKLOG.md |
| In Roadmap | Sudah masuk iterasi aktif |
| Resolved | Sudah diselesaikan |
| Rejected | Tidak akan dikerjakan |
| Duplicate | Duplikasi dari issue lain |

---

## OPEN ISSUES

*(Tidak ada issue terbuka saat ini)*

---

## RESOLVED ISSUES

### ISS-001 — Dashboard Masih Tracking-Centric

**Status:** Resolved
**Diselesaikan di:** Iterasi 04
**Kategori:** UX / UI Prioritization
**Sumber:** Gemini UX Review (raw/001.md)

Grafik tren 7 hari diubah dari mengukur jarak (km) menjadi mengukur jumlah temuan per hari.

---

### ISS-002 — Urutan Summary Cards di Daftar Perjalanan Bias ke Tracking

**Status:** Resolved
**Diselesaikan di:** Iterasi 04
**Kategori:** UX / UI Prioritization
**Sumber:** Gemini UX Review (raw/001.md)

Summary cards di `/activities` dan `/dashboard` diurutkan ulang: Temuan → Foto → Total Perjalanan → Total Jarak.

---

### ISS-003 — Indikator Progress Bar Kartu Aktivitas Berbasis Track Point

**Status:** Resolved
**Diselesaikan di:** Iterasi 04
**Kategori:** UX / Visual Logic
**Sumber:** Gemini UX Review (raw/001.md)

Formula progress bar diubah menggunakan bobot pengali: temuan ×10, foto ×5, track point ×1.

---

### ISS-004 — Grafik Tren 7 Hari Dashboard Mengukur Jarak, Bukan Temuan

**Status:** Resolved
**Diselesaikan di:** Iterasi 04
**Kategori:** UX / Dashboard Metric
**Sumber:** Gemini UX Review (raw/001.md)

Widget Hero Dashboard diubah menggunakan `orderByDesc('events_count')` — menampilkan perjalanan terkaya temuan, bukan terjauh.

---

### ISS-005 — Opsi Filter & Sorting Daftar Perjalanan Belum Optimal

**Status:** Resolved
**Diselesaikan di:** Iterasi 04
**Kategori:** UX / Feature Gap
**Sumber:** Gemini UX Review (raw/001.md)

Filter "Hanya perjalanan dengan temuan" ditambahkan menggunakan `whereHas` di `ActivityController`. Sorting "Temuan terbanyak" dipromosikan ke posisi menonjol di dropdown.

---

## REJECTED ISSUES

*(Tidak ada)*
