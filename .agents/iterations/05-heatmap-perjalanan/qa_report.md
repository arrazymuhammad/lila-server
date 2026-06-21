# QA Report
## Iterasi 05 — Heatmap Perjalanan

**Status: PASS ✅**

> Tanggal: 2026-06-21

---

## Ringkasan Pengujian

Seluruh item dalam QA Checklist Iterasi 05 telah diuji dan lulus. Semua 10 acceptance criteria terpenuhi. Tidak ada bug yang ditemukan. Tidak ada regresi pada halaman-halaman yang tidak diubah. Fitur heatmap berfungsi penuh: rendering, toggle, persistensi localStorage, interaksi antar-toggle, dan filter periode.

---

## Hasil Checklist

| Area | Hasil |
|------|-------|
| Toggle Heatmap — Aktivasi | ✅ 7/7 Lulus |
| Toggle Heatmap — Deaktivasi | ✅ 3/3 Lulus |
| State Persistensi localStorage | ✅ 3/3 Lulus |
| Interaksi Heatmap + Toggle Temuan | ✅ 3/3 Lulus |
| Filter Bulan & Tahun + Heatmap | ✅ 3/3 Lulus |
| Edge Case | ✅ 2/2 Lulus |
| UI Test | ✅ 7/7 Lulus |
| Regression Test | ✅ 11/11 Lulus |
| Acceptance Criteria | ✅ 10/10 Lulus |

---

## Bug Yang Ditemukan

**Tidak ada bug yang ditemukan.**

---

## Catatan QA

### 1. CDN Dependency Diterima
Library `leaflet-heat.js` dimuat via CDN `unpkg.com` — konsisten dengan pendekatan Leaflet utama yang sudah ada. Dalam sesi QA ini, CDN berhasil dimuat tanpa masalah. Risiko offline tetap terdokumentasi di `iteration.md` (Risiko #2) dan diterima untuk iterasi ini.

### 2. Performa Render Normal
Tidak ada lag terasa pada browser saat rendering heatmap dengan data perjalanan pada periode yang diuji. Risiko performa di skala masif tetap terdokumentasi sebagai catatan jangka panjang.

### 3. Integrasi Non-Destruktif Terkonfirmasi
Toggle Heatmap dan toggle Semua Temuan bekerja sepenuhnya independen — tidak ada interferensi state antar keduanya. Marker temuan tetap dirender di atas layer heatmap sesuai spesifikasi.

---

## Risiko Yang Masih Ada

| # | Risiko | Tingkat | Keterangan |
|---|--------|---------|------------|
| 1 | Performa render pada volume track points yang sangat besar | Rendah | Tidak terjadi pada volume data saat ini; perlu dipantau seiring pertumbuhan data |
| 2 | Ketergantungan CDN eksternal (unpkg.com) | Rendah | Diterima untuk iterasi ini; dapat dimitigasi dengan asset lokal jika diperlukan di masa mendatang |
| 3 | BL-001, BL-002 dari backlog | Sedang | Tidak berubah dari iterasi sebelumnya — menunggu keputusan infrastruktur |
| 4 | BL-009 Manajemen Kategori Master | Sedang | Semakin mendesak; dijadwalkan sebagai Iterasi 06 |
