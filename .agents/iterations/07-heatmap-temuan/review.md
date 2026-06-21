# Pre-Implementation Review
## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

> Status: **CLEAR — Siap Diimplementasikan**

---

## 1. Apakah Ruang Lingkup Sudah Jelas?

**Ya.** Scope dibatasi pada dua file: `MapController.php` dan `maps/index.blade.php`. Tidak ada perubahan schema, routing, atau API. Titik integrasi dengan data yang sudah ada sudah diidentifikasi secara spesifik:

- `activity_events.operator_category` (string) → filter di AlpineJS client-side.
- `finding_categories` (master) → sumber dropdown kategori.
- Koordinat `latitude`/`longitude` dari `activity_events` → sumber titik heatmap.

---

## 2. Apakah Ada Konflik Dengan Sistem Yang Ada?

Tidak ada konflik destruktif. Satu hal yang perlu diperhatikan:

- **`MapController.php` saat ini tidak meng-expose `operator_category`** dalam array `findings` yang dikirim ke view. Ini adalah satu-satunya perubahan backend yang diperlukan — menambahkan satu field ke mapping yang sudah ada.
- `leaflet-heat.js` sudah terpasang via CDN di `maps/index.blade.php` (Iterasi 05). Tidak perlu menambah dependensi baru.
- AlpineJS component `allRoutesMap()` sudah ada dengan pola toggle dan `localStorage` yang terbukti bekerja. Penambahan state baru (`selectedCategory`, `showFindingHeatmap`, `heatFindingLayer`) mengikuti pola yang persis sama.

---

## 3. Apakah Ada Library Yang Perlu Ditambahkan?

**Tidak ada.** `leaflet-heat.js` sudah tersedia via CDN di file view. AlpineJS sudah terpasang global di layout. Tidak ada `npm install` atau `composer require` yang diperlukan.

---

## 4. Apakah Ada Risiko Yang Belum Disebutkan?

Satu risiko tambahan yang perlu dicatat:

- **Filter client-side vs server-side:** Desain yang dipilih adalah filter kategori dilakukan di sisi *client* (AlpineJS menyaring array `findings` sebelum meneruskan ke `L.heatLayer`). Ini berarti **semua data temuan untuk bulan/tahun terpilih di-load ke browser sekaligus**. Jika volume data sangat besar (ribuan temuan per bulan), ini bisa menyebabkan halaman lambat. Untuk volume data LILA saat ini (sistem monitoring lapangan terbatas), ini *dapat diterima*. Jika skala meningkat, bisa dipertimbangkan untuk dipindahkan ke server-side AJAX filter — tapi ini bukan scope sekarang.

---

## 5. Estimasi File Yang Berubah

| File | Jenis Perubahan | Risiko |
|------|-----------------|--------|
| `app/Http/Controllers/MapController.php` | Tambah field `operator_category` ke array `findings`, tambah query `FindingCategory`, pass `$categories` ke view | Sangat Rendah |
| `resources/views/maps/index.blade.php` | Tambah dropdown kategori, tambah toggle heatmap temuan, tambah logika AlpineJS | Rendah |

**Total: 2 file.**

---

## 6. Urutan Implementasi Yang Disarankan

1. **`MapController.php`**: Tambahkan `operator_category` ke dalam `findings` array, query `FindingCategory`, pass `$categories`.
2. **`maps/index.blade.php` — State AlpineJS**: Tambahkan `selectedCategory`, `showFindingHeatmap`, `heatFindingLayer` ke dalam object `allRoutesMap()`.
3. **`maps/index.blade.php` — Method**: Implementasikan `renderFindingHeatmap()` yang memfilter `findings` berdasarkan `selectedCategory` dan membuat `L.heatLayer`.
4. **`maps/index.blade.php` — `initMap()`**: Panggil `renderFindingHeatmap()` saat init jika `showFindingHeatmap` aktif.
5. **`maps/index.blade.php` — UI**: Tambahkan dropdown kategori dan toggle Heatmap Temuan di header.
6. **Test manual** sesuai QA checklist iterasi.

---

## 7. Kesimpulan Status Review

**CLEAR — Siap Diimplementasikan.**

Tidak ada ambiguitas, tidak ada dependensi yang hilang, tidak ada risiko tinggi. Semua infrastruktur (Leaflet, leaflet-heat, AlpineJS, master kategori, koordinat temuan) sudah tersedia. Perubahan bersifat adiktif dan mengikuti pola yang sudah terbukti di iterasi sebelumnya.
