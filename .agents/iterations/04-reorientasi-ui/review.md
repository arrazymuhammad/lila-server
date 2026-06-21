# PRE-IMPLEMENTATION REVIEW
## Iterasi 04 — Reorientasi UI Observation-Centric

> Tanggal Review: 2026-06-21
> Reviewer: Technical Reviewer
> Status: **CLEAR — Siap untuk implementasi**

---

## 1. Apakah Ruang Lingkup Sudah Jelas?

**Ya, ruang lingkup sangat jelas dan terdefinisi dengan baik.**

Setiap perubahan sudah dipetakan ke file dan baris kode spesifik berdasarkan audit aktual. Tidak ada ambiguitas mengenai apa yang harus diubah, di mana, dan mengapa.

---

## 2. Apakah Ada Konflik dengan Sistem Saat Ini?

### ✅ Tidak Ada Konflik Berisiko

**`DashboardController`** — Data `events_count` sudah tersedia melalui query yang ada (`ActivityEvent::where('status', 'verified')`). Perubahan yang diperlukan:
- Tambah `events_count` ke setiap item di `activityTrend` (perlu menambah `withCount` di `recentSessions`).
- Ganti `orderByDesc('distance')` → `orderByDesc('events_count')` di `$highlightSession`.
- Ganti `maxTrendDistance` → `maxTrendEvents`.

Semua perubahan bersifat additive atau substitutif — tidak ada risiko regresi pada fungsionalitas lain.

**`ActivityController`** — Query sudah menyertakan `withCount(['events', 'photos', 'trackPoints'])`. Semua data yang dibutuhkan untuk formula progress bar baru sudah ada. Hanya perlu:
- Tambah filter `has_findings` (1 kondisi `whereHas`).
- Tidak perlu mengubah query utama.

**View** — Perubahan hanya pada urutan render dan formula kalkulasi nilai bar. Tidak ada struktur Blade yang perlu diubah secara fundamental.

### ⚠️ Satu Hal yang Perlu Diperhatikan

**`recentSessions` di DashboardController (baris 59-63)** saat ini hanya mengambil kolom `['start_time', 'distance']`:
```php
->get(['start_time', 'distance']);
```
Untuk menambahkan `events_count` ke trend harian, query ini perlu diperluas dengan `withCount`. Namun karena `events_count` adalah hasil agregasi (bukan kolom), tidak bisa hanya ditambahkan di `get()`. Perlu mengubah query ini agar menggunakan `withCount` sebelum `get()`.

---

## 3. Apakah Ada Risiko yang Belum Disebutkan?

### Risiko A — Grafik Kosong di Fase Awal (Sudah Disebutkan, Konfirmasi)
Karena banyak temuan masih berstatus `submitted`, nilai `events_count` per hari akan sangat kecil atau nol untuk beberapa waktu ke depan. Grafik akan tampak hampir kosong. **Ini bukan bug** — perlu dipastikan ada handling visual yang informatif (misalnya: label "Belum ada temuan terverifikasi hari ini").

### Risiko B — Widget Hero Mungkin Menampilkan Session Tanpa Temuan (Baru)
Jika `orderByDesc('events_count')` diterapkan tetapi semua session memiliki `events_count = 0`, widget hero akan menampilkan session pertama secara arbitrer. Perlu fallback: jika tidak ada session dengan temuan, sembunyikan widget hero atau tampilkan pesan "Belum ada perjalanan dengan temuan terverifikasi".

### Risiko C — Formula Progress Bar dengan Semua Nilai Nol (Sudah Disebutkan, Konfirmasi)
Jika semua kartu dalam satu halaman memiliki `events_count = 0` dan `photos_count = 0`, semua bar akan panjang = 0. Fallback ke `track_points_count` dalam kondisi ini sudah direkomendasikan di `iteration.md`.

---

## 4. Estimasi Jumlah File yang Berubah

| # | File | Jenis Perubahan | Kompleksitas |
|---|------|-----------------|-------------|
| 1 | `app/Http/Controllers/DashboardController.php` | MODIFY — 3 titik perubahan kecil | Rendah |
| 2 | `app/Http/Controllers/ActivityController.php` | MODIFY — 1 kondisi filter tambahan | Sangat Rendah |
| 3 | `resources/views/dashboard.blade.php` | MODIFY — urutan cards, sumbu grafik | Rendah |
| 4 | `resources/views/activities/index.blade.php` | MODIFY — urutan cards, formula bar, toggle filter | Rendah-Sedang |

**Total: 4 file** — Di bawah ambang batas, scope terkendali dengan baik.

---

## 5. Area Kode yang Kemungkinan Terdampak

| Area | Dampak | Level |
|------|--------|-------|
| Dashboard grafik tren | Sumbu Y berubah dari jarak → temuan | Rendah |
| Dashboard widget hero | Session yang ditampilkan berubah | Rendah |
| Dashboard summary cards | Hanya urutan render di view | Sangat Rendah |
| Activities summary cards | Hanya urutan render di view | Sangat Rendah |
| Activities progress bar | Formula kalkulasi di view | Rendah |
| Activities filter | Tambah 1 kondisi query | Sangat Rendah |

---

## Kesimpulan Review

**Implementasi dapat dimulai tanpa blocker.**

Ini adalah iterasi dengan **risiko teknis paling rendah** sejauh ini. Hampir semua data yang dibutuhkan sudah tersedia di backend — sebagian besar perubahan adalah di layer view (Blade template). Satu-satunya perubahan backend yang perlu perhatian adalah penambahan `withCount` di query `recentSessions` pada `DashboardController`.

**Urutan implementasi yang disarankan:**
1. `DashboardController` — ubah `recentSessions`, `activityTrend`, `highlightSession`
2. `ActivityController` — tambah filter `has_findings`
3. `dashboard.blade.php` — reorder cards, ganti sumbu grafik
4. `activities/index.blade.php` — reorder cards, formula bar, toggle filter
