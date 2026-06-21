# Iterasi 08 — Peta Interaktif: Rich Finding Popup

> Status: **Proposed**
> Target Mulai: Pasca Persetujuan Pipeline
> Fitur: Popup Preview Temuan (foto, deskripsi, kategori) dari halaman Peta

---

## 1. Pertimbangan Backlog vs Roadmap

Berdasarkan `sprint_review.md` Iterasi 07, permintaan fitur ini pertama kali diidentifikasi saat QA oleh user sebagai kebutuhan interaksi yang mendesak. Tidak ada backlog prioritas lebih tinggi yang harus didahulukan.

Fitur ini meningkatkan nilai fungsional dari peta utama (yang sudah memiliki heatmap dan marker) tanpa memerlukan infrastruktur baru. Semua data yang diperlukan sudah ada di database.

**Kesimpulan:** Iterasi 08 (Rich Popup) dapat dieksekusi langsung.

---

## 2. Latar Belakang

Berdasarkan audit kode aktual:

**Kode yang sudah ada:**
- [`MapController.php`](app/Http/Controllers/MapController.php): Sudah eager-load relasi `events.photos` dengan `where('selected', true)`. Tapi field `description`, `timestamp`, dan `photos` **tidak dimasukkan ke dalam payload `$routes`** yang di-`@json` ke view.
- [`ActivityEvent.php`](app/Models/ActivityEvent.php): Model sudah memiliki `description` dan `timestamp` sebagai `fillable`.
- [`ActivityPhoto.php`](app/Models/ActivityPhoto.php): Model memiliki `file_path`, `thumbnail_path`, `filename`, `selected`.
- [`maps/index.blade.php`](resources/views/maps/index.blade.php): Marker temuan sudah menggunakan `L.circleMarker(...).bindPopup(...)` dengan template HTML string sederhana.

**Gap yang perlu diisi:**
1. Payload `findings` di `MapController` perlu ditambahkan: `description`, `timestamp`, `photos[]` (array URL foto).
2. Template popup HTML di AlpineJS `renderRoutes()` perlu diperkaya menjadi card dengan foto, deskripsi, waktu, dan kategori.
3. Jika foto > 1, perlu logika carousel (prev/next dengan plain JavaScript, karena AlpineJS tidak beroperasi di dalam Leaflet popup).

---

## 3. Masalah yang Diselesaikan

1. **Popup saat ini terlalu minimal:** Hanya menampilkan status dan link. Operator harus membuka tab baru hanya untuk melihat foto atau deskripsi singkat sebuah temuan.
2. **Konteks hilang di peta:** Operator tidak dapat langsung menilai tingkat keparahan temuan dari peta tanpa navigasi ke halaman lain.

---

## 4. Tujuan Iterasi

1. Memperkaya HTML template popup Leaflet menjadi *card preview* mini yang menampilkan:
   - Foto utama (atau carousel jika lebih dari 1)
   - Judul temuan
   - Kategori (badge)
   - Deskripsi singkat (truncated)
   - Waktu temuan
2. Mempertahankan link "Detail Temuan" tetap tersedia di popup.
3. Tidak merusak fitur heatmap, toggle, dan filter yang sudah ada.

---

## 5. Ruang Lingkup

### Backend (`MapController.php`)

- **[MODIFY]** [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php)
  - Tambahkan ke dalam array per-finding: `description`, `timestamp`, `photos[]` (array URL string yang dibangun dari `file_path` atau `thumbnail_path`).
  - Perlu investigasi: apakah file foto disimpan di `storage/app/public/` atau `public/uploads/`? Gunakan `Storage::url()` atau `url()` sesuai konfigurasi yang ada.

### Frontend (`maps/index.blade.php`)

- **[MODIFY]** [`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php)
  - Ganti template popup HTML dari string sederhana menjadi card HTML yang kaya.
  - Implementasikan carousel mini dengan state `currentPhotoIndex` menggunakan plain JavaScript (bukan AlpineJS) di dalam `bindPopup()` / `popupopen` event Leaflet, karena Leaflet popup DOM dikelola di luar AlpineJS.

### Routing

- Tidak ada perubahan routing.

---

## 6. Yang Tidak Termasuk (Out of Scope)

- Tidak ada perubahan schema database.
- Tidak ada penambahan endpoint API baru.
- Popup hanya untuk peta utama (`/map`). Bukan untuk halaman peta perjalanan individual di `/activities/{session}`.
- Tidak ada edit/action dari dalam popup.

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Popup marker temuan menampilkan: foto, judul, kategori (badge), deskripsi singkat, timestamp.
- [ ] Jika ada lebih dari 1 foto, tersedia tombol navigasi foto (prev/next carousel mini).
- [ ] Link "Detail Temuan" tetap ada di popup.
- [ ] Popup tidak menyebabkan error JavaScript saat marker di-klik.
- [ ] Fitur heatmap (rute dan temuan) + filter kategori tetap berjalan normal (non-destructive).

---

## 8. Risiko dan Hal yang Perlu Diperhatikan

| # | Risiko | Dampak | Mitigasi |
|---|--------|--------|----------|
| 1 | AlpineJS tidak bisa langsung mengelola DOM di dalam popup Leaflet | Carousel tidak bisa pakai AlpineJS | Gunakan plain JS event listener pada `map.on('popupopen', ...)` |
| 2 | Path foto berbeda per environment | URL foto rusak di production | Perlu audit `storage_path()` atau `asset()` pattern yang sudah digunakan di view lain |
| 3 | Popup terlalu panjang untuk layar kecil | UX buruk di mobile web | Tetapkan `max-height` dan `overflow-y: auto` pada elemen popup |
| 4 | JSON payload membengkak signifikan jika foto per temuan banyak | Halaman lambat saat load | Batasi hanya `selected: true` photos (sudah ada filter ini), dan gunakan `thumbnail_path` jika tersedia |

---

## 9. Pra-Implementasi Yang Diperlukan

Sebelum implementasi, developer perlu melakukan **investigasi cara foto disimpan** — tepatnya bagaimana value `file_path` di `activity_photos` dikonversi menjadi URL yang bisa diakses via browser. Perlu dicek di view-view yang sudah ada (misal: `findings/show.blade.php`) untuk melihat pola yang sudah digunakan.

---

## 10. Dampak Terhadap Pengguna

- Operator dapat segera menilai foto dan konteks temuan langsung dari peta tanpa perlu membuka tab baru.
- Meningkatkan efisiensi kerja reviewer/monitoring lapangan secara signifikan.
- Tidak ada perubahan pada alur verifikasi atau penginputan data.
