# Implementation Report

## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

| Status | Selesai |
|---|---|
| **Tanggal Implementasi** | Hari ini |
| **Branch/Environment** | Local (`lila.test`) |

---

## Ringkasan Implementasi

Implementasi Iterasi 07 telah selesai sesuai `iteration.md`. Fitur "Heatmap Temuan Berdasarkan Kategori" berhasil diintegrasikan ke halaman Peta (`/map`) tanpa mengubah relasi database atau merusak fitur yang sudah ada. 

Fokus perubahan adalah mengekspos field `operator_category` dari tabel `activity_events` (lewat `MapController`) ke view, lalu melakukan filter dan *mapping* ke layer `L.heatLayer` di sisi client menggunakan AlpineJS dan `leaflet-heat.js`.

---

## File Yang Diubah

### 1. `app/Http/Controllers/MapController.php`
- **Tujuan**: Menyediakan data kategori untuk filter.
- **Perubahan**:
  - Mengimpor `App\Models\FindingCategory`.
  - Mengambil daftar master kategori dengan `FindingCategory::orderBy('name')->get()`.
  - Menambahkan field `'operator_category' => $event->operator_category` ke payload array `$routes->findings`.

### 2. `resources/views/maps/index.blade.php`
- **Tujuan**: Menampilkan UI toggle, dropdown filter, dan merender layer peta.
- **Perubahan**:
  - Menambahkan UI toggle switch "Heatmap Temuan" dengan warna distinct (ungu).
  - Menambahkan dropdown `<select>` "Kategori Temuan" yang hanya muncul ketika toggle Heatmap Temuan aktif (`x-show="showFindingHeatmap"`).
  - Menambahkan state AlpineJS: `showFindingHeatmap` (persisten via `localStorage`) dan `selectedCategory`.
  - Modifikasi logika `renderRoutes()`: jika `showFindingHeatmap` aktif, marker individual tidak digambar, digantikan pengumpulan koordinat ke dalam `findingHeatPoints`. Array ini difilter berdasarkan `selectedCategory`.
  - Menambahkan render layer `L.heatLayer` khusus temuan dengan custom gradient (`purple`, `fuchsia`, `red`, `yellow`) agar dapat dibedakan dari heatmap rute perjalanan.

---

## Catatan Deviasi

- **Custom Gradient**: Diberikan instruksi pewarnaan gradien khusus (`{0.4: 'purple', 0.6: 'fuchsia', 0.8: 'red', 1: 'yellow'}`) untuk layer heatmap temuan. Ini agar jika pengguna menyalakan Heatmap Rute (biru-merah default) dan Heatmap Temuan bersamaan, keduanya masih bisa dibedakan secara visual.

---

## Risiko / Keterbatasan (Diterima)

- Sesuai dengan batasan yang sudah didokumentasikan di pra-implementasi, pencocokan kategori dilakukan secara *exact string match* di client-side (`finding.operator_category === this.selectedCategory`). Temuan lama (pre-Iterasi 06) yang diisi bebas mungkin tidak ter-filter ke dalam dropdown master kategori. Solusinya membutuhkan *data maintenance* (BL-011) di masa depan.
