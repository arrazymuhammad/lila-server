# Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

> Status: **Proposed**
> Target Mulai: Pasca Persetujuan Pipeline
> Fitur: Visualisasi Heatmap Titik Temuan Berdasarkan Kategori

---

## 1. Pertimbangan Backlog vs Roadmap

Berdasarkan `roadmap_review_004.md` dan `backlog_review.md` pasca Iterasi 06:

- Roadmap Iterasi 07 (Heatmap Temuan) sudah direncanakan sejak awal.
- QA Iterasi 06 secara eksplisit mengusulkan fitur ini sebagai kebutuhan lebih relevan dari heatmap trackpoint.
- Fondasi data sudah siap: tabel `finding_categories` (Iterasi 06) + koordinat `latitude`/`longitude` di `activity_events`.
- Tidak ada backlog item yang lebih mendesak untuk menyela iterasi ini.

**Kesimpulan:** Lanjutkan Iterasi 07 sesuai roadmap.

---

## 2. Latar Belakang

Berdasarkan audit kode aktual:

- **`MapController.php`**: Sudah meng-eager-load data `events` beserta koordinatnya per sesi, dan mengeksposnya ke `$routes` sebagai array `findings` (dengan `latitude`, `longitude`, `status`, dan `id`). Namun, field `operator_category` **belum disertakan** dalam payload ini.
- **`maps/index.blade.php`**: Sudah memiliki toggle heatmap (Iterasi 05) untuk trackpoint-based density. Heatmap perjalanan sudah menggunakan `leaflet-heat.js` via CDN. Layer `L.heatLayer` sudah diimplementasikan di AlpineJS component `allRoutesMap()`. Tidak ada layer khusus untuk finding-based heatmap.
- **`ActivityEvent.php`**: Field `operator_category` sudah ada sebagai `fillable`, bertipe string.

Kondisi saat ini: Heatmap yang ada hanya memvisualisasikan **kepadatan trackpoint (GPS)** — yaitu rute perjalanan. Temuan lapangan (events) hanya divisualisasikan sebagai **dot/marker** individual, bukan sebagai layer densitas. Tidak ada cara untuk melihat konsentrasi geografis temuan berdasarkan kategori spesifik.

---

## 3. Masalah yang Diselesaikan

1. **Heatmap trackpoint kurang relevan secara bisnis:** Densitas trackpoint hanya menggambarkan kepadatan patroli (GPS), bukan konsentrasi masalah lapangan.
2. **Tidak ada visualisasi spasial temuan per kategori:** Operator tidak dapat melihat "di mana konsentrasi temuan 'Alat Tangkap Terlarang'?" atau "apakah temuan 'Pencemaran Minyak' terkluster di wilayah tertentu?".
3. **Marker individual temuan tidak skalabel:** Ketika ratusan temuan muncul di peta, marker individual menjadi tumpang tindih dan sulit diinterpretasikan.

---

## 4. Tujuan Iterasi

1. Menambahkan layer **Heatmap Temuan** ke halaman `/map` menggunakan `leaflet-heat.js` yang sudah terpasang.
2. Menyediakan **filter kategori** pada layer heatmap temuan, menggunakan data dari tabel `finding_categories`.
3. Mempertahankan semua fungsionalitas yang sudah ada (heatmap trackpoint, toggle temuan, filter bulan/tahun) tanpa regresi.

---

## 5. Ruang Lingkup

### Backend (`MapController.php`)

- **[MODIFY]** [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php)
  - Tambahkan `operator_category` ke dalam array `findings` di dalam `$routes` mapping.
  - Tambahkan query `FindingCategory::orderBy('name')->get(['name'])` untuk menyediakan daftar master kategori ke view.
  - Pass variabel `$categories` ke view.

### Frontend (`maps/index.blade.php`)

- **[MODIFY]** [`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php)
  - Tambahkan dropdown filter **Kategori** di panel kontrol header (sejajar dengan filter Bulan/Tahun).
  - Tambahkan state AlpineJS: `selectedCategory` (default: `''` = semua kategori).
  - Tambahkan state: `showFindingHeatmap` (toggle layer heatmap temuan, terpisah dari toggle heatmap trackpoint).
  - Tambahkan toggle UI untuk Heatmap Temuan (mirip toggle Heatmap Perjalanan).
  - Implementasikan `heatFindingLayer` (`L.heatLayer`) menggunakan koordinat `findings` yang difilter berdasarkan `selectedCategory`.
  - Persistensi `showFindingHeatmap` menggunakan `localStorage` (`lila_show_finding_heatmap`).
  - Ketika `selectedCategory` berubah → refresh `heatFindingLayer` (tidak perlu full page reload).

### Routing

- Tidak ada perubahan routing.

---

## 6. Yang Tidak Termasuk (Out of Scope)

- Tidak ada perubahan schema database.
- Tidak ada perubahan pada API sinkronisasi mobile.
- Tidak ada perubahan pada form verifikasi, filter daftar temuan, atau halaman categories.
- Tidak ada normalisasi data kategori lama (disarankan sebagai BL-011 — Low priority).
- Tidak ada warna berbeda per kategori di heatmap (mono-color density — fitur lanjutan jika diperlukan).

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Dropdown filter "Kategori" di header halaman `/map` menampilkan daftar kategori dari tabel master.
- [ ] Toggle "Heatmap Temuan" berfungsi dan menampilkan density layer berdasarkan koordinat temuan.
- [ ] Memilih kategori dari dropdown memfilter titik heatmap temuan secara real-time (tanpa reload).
- [ ] Toggle heatmap temuan dapat dimatikan/dinyalakan, dan state-nya persisten di `localStorage`.
- [ ] Heatmap trackpoint (Iterasi 05) tetap berfungsi normal dan tidak terganggu.
- [ ] Halaman `/map` tidak terdapat error JavaScript di console.
- [ ] Filter bulan/tahun masih berfungsi beriringan dengan heatmap temuan.
- [ ] Regression test: seluruh fitur Iterasi 01–06 tetap berjalan normal.

---

## 8. Risiko dan Hal yang Perlu Diperhatikan

| # | Risiko | Dampak | Mitigasi |
|---|--------|--------|----------|
| 1 | Temuan lama (pre-Iter 06) memiliki `operator_category` sebagai *free text* yang mungkin tidak cocok persis dengan master | Heatmap filter mungkin tidak menangkap semua data lama | Dokumentasikan sebagai batasan; filter menggunakan string exact-match |
| 2 | Temuan tanpa koordinat valid tidak muncul di heatmap | Data loss visual | `filter()` koordinat non-null sudah ada di `MapController`, cukup diperluas |
| 3 | Jumlah kategori yang banyak membuat dropdown filter terlalu panjang | UX dropdown panjang | Wajar, karena kategori adalah master data terbatas |

---

## 9. Dampak Terhadap Pengguna

- Operator mendapat alat analitik spasial baru: melihat **di mana** masalah lapangan terkonsentrasi per kategori.
- Tidak ada perubahan pada alur kerja verifikasi atau penginputan data.
- Perubahan hanya bersifat adiktif (menambahkan fitur, tidak menghapus).
