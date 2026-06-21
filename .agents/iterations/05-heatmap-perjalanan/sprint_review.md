# Sprint Review
## Iterasi 05 — Heatmap Perjalanan

> Tanggal: 2026-06-21
> Status: **Completed — Clean ✅**

---

## Ringkasan

Iterasi 05 selesai bersih — satu file diubah, tidak ada bug, tidak ada regresi. Heatmap Perjalanan kini tersedia di halaman `/map` sebagai layer visualisasi alternatif yang memberikan gambaran instan tentang densitas cakupan wilayah patroli. Operator dapat langsung mengidentifikasi hotspot (area sering dilintasi) dan coldspot (area yang jarang atau belum tersentuh patroli) tanpa harus menganalisis puluhan polyline satu per satu.

---

## Tujuan Yang Tercapai

- [x] Library `leaflet-heat.js` terintegrasi tanpa instalasi npm baru — via CDN, konsisten dengan Leaflet utama.
- [x] Toggle "Heatmap" tersedia di halaman `/map` dengan UX yang konsisten (rose saat aktif, abu-abu saat OFF).
- [x] Heatmap dirender dari akumulasi track points semua perjalanan terverifikasi pada periode terpilih.
- [x] Mode polyline standar tetap berfungsi penuh saat Heatmap dimatikan.
- [x] State persistensi via `localStorage` — preferensi pengguna tersimpan antar sesi.
- [x] Integrasi non-destruktif — toggle Semua Temuan dan filter bulan/tahun tetap bekerja normal.
- [x] Tidak ada perubahan pada API mobile, database, atau halaman lain.

---

## Tujuan Yang Belum Tercapai

Tidak ada. Iterasi ini selesai 100%.

---

## Pembelajaran

1. **Pola CDN Leaflet plugin terbukti efektif.** Menambahkan plugin Leaflet via CDN (tanpa npm) adalah pendekatan yang tepat untuk menghindari perubahan pada area Forbidden (`package.json`). Pola ini dapat diulang untuk plugin Leaflet lainnya di iterasi mendatang (misal: Leaflet.markercluster untuk Iterasi 07).

2. **AlpineJS `refreshMap()` pattern scalable.** Pola membersihkan semua layer aktif (`mapLayers.forEach(layer => map.removeLayer(layer))`) sebelum render ulang terbukti menjadi solusi yang bersih untuk mengelola multiple layer types. Pattern ini sudah siap digunakan kembali untuk Heatmap Temuan (Iterasi 07).

3. **Implementasi frontend-only = scope terkontrol dan risiko minimal.** Iterasi yang hanya menyentuh satu file view menghasilkan QA yang sangat cepat dan tidak ada risiko regresi pada sistem inti.

---

## Risiko Yang Masih Terbuka

| # | Risiko | Tingkat |
|---|--------|---------|
| 1 | Performa render pada volume track points yang sangat besar | Rendah |
| 2 | Ketergantungan CDN eksternal (unpkg.com) | Rendah |
| 3 | BL-009 Manajemen Kategori Master — data terus bertambah tanpa master | Sedang |
| 4 | BL-001, BL-002 — menunggu keputusan infrastruktur | Sedang |

---

## Evaluasi Roadmap

```
✅ Iteration 1:   Verifikasi Perjalanan              [SELESAI]
✅ Iteration 2:   Visibility Rule                    [SELESAI]
✅ Iteration 3-A: Verifikasi Temuan (Inti)           [SELESAI]
✅ Iteration 3-B: Pengayaan Kategori Temuan          [SELESAI]
✅ Iteration 4:   Reorientasi UI Observation-Centric [SELESAI — CLEAN]
✅ Iteration 5:   Heatmap Perjalanan                 [SELESAI — CLEAN]
📋 Iteration 6:   Manajemen Kategori Master          [BERIKUTNYA]
📋 Iteration 7:   Heatmap Temuan                     [DIRENCANAKAN]
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan   [DIRENCANAKAN]
```

Roadmap tetap relevan. Iterasi 06 (Manajemen Kategori Master) tidak memiliki prasyarat teknis yang belum terpenuhi dan siap direncanakan.

---

## Rekomendasi Iterasi Berikutnya

**Lanjut ke Iterasi 06 — Manajemen Kategori Master** sesuai roadmap.

BL-009 semakin mendesak — data `operator_category` terus diisi sebagai teks bebas sejak Iterasi 03-B tanpa validasi master. Semakin lama ditunda, semakin banyak data yang perlu dibersihkan.

---

## Keputusan

- **Lanjut roadmap** — Iterasi 06 (Manajemen Kategori Master) menjadi prioritas berikutnya.
- **Jalankan pipeline standar** — roadmap review → backlog review → maintenance → iteration.md Iterasi 06.
