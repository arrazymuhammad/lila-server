# Iterasi 06 — Manajemen Kategori Master

> Status: **Proposed — Menunggu Persetujuan Infrastructure**
> Target Mulai: Pasca Persetujuan Schema Database
> Fitur: Manajemen Tabel Master Kategori Temuan

---

## ⚠️ Peringatan Infrastructure

**Iterasi ini memerlukan pembuatan tabel database baru (`finding_categories`).**

Berdasarkan `AI_CONTEXT.md` Critical Rules:

> JANGAN melakukan perubahan berikut tanpa persetujuan eksplisit:
> - Database schema
> - Migration

**Implementasi tidak boleh dimulai sebelum ada konfirmasi dari admin/owner bahwa tabel `finding_categories` diizinkan untuk dibuat.**

Dua opsi scope tersedia — lihat Bagian 5.

---

## 1. Pertimbangan Backlog vs Roadmap

BL-009 sudah dijadwalkan sebagai Iterasi 06 sejak `roadmap_review_002.md`. Audit kode pada `roadmap_review_004.md` mengkonfirmasi urgensinya:

- [`Verification/FindingController::review()`](../../../app/Http/Controllers/Verification/FindingController.php) mengambil auto-suggest langsung dari `DISTINCT operator_category` — tidak ada validasi terhadap daftar baku.
- Data `operator_category` di tabel `activity_events` terus diisi sebagai teks bebas sejak Iterasi 03-B tanpa kontrol ejaan.
- Heatmap Temuan (Iterasi 07) bergantung pada kualitas kategori yang bersih.

Tidak ada backlog lain yang lebih mendesak tanpa hambatan infrastruktur/API mobile yang belum diputuskan.

**Keputusan:** Iterasi 06 — Manajemen Kategori Master adalah iterasi berikutnya sesuai roadmap.

---

## 2. Latar Belakang

### Kondisi Kode Aktual

Berdasarkan audit pada:
- [`app/Http/Controllers/Verification/FindingController.php`](../../../app/Http/Controllers/Verification/FindingController.php)
- [`app/Http/Controllers/FindingController.php`](../../../app/Http/Controllers/FindingController.php)
- [`app/Models/ActivityEvent.php`](../../../app/Models/ActivityEvent.php)
- [`resources/views/verifications/findings/review.blade.php`](../../../resources/views/verifications/findings/review.blade.php)
- [`resources/views/findings/show.blade.php`](../../../resources/views/findings/show.blade.php)
- [`routes/web.php`](../../../routes/web.php)

**Kondisi saat ini:**

1. Kolom `operator_category` ada di tabel `activity_events` (teks bebas, nullable).
2. Auto-suggest di form verifikasi membaca `DISTINCT operator_category ORDER BY operator_category` — artinya setiap nilai unik yang pernah diketik operator menjadi saran, termasuk typo dan variasi ejaan.
3. Tidak ada tabel master kategori (`finding_categories` atau sejenisnya).
4. Tidak ada filter by `operator_category` di halaman `/findings` — operator tidak dapat menyaring temuan berdasarkan kategori.
5. Tidak ada CRUD controller untuk mengelola daftar kategori.
6. Tidak ada route yang mengarah ke manajemen kategori.

**Risiko yang terkonfirmasi:**
- "Jaring Ilegal", "jaring ilegal", "Jaring ilegal", "Jaring Ikan Illegal" — semua muncul sebagai entri terpisah di auto-suggest, dan semua tersimpan sebagai kategori yang berbeda di database.
- Analisis statistik kategori dan Heatmap Temuan (Iterasi 07) tidak akan akurat selama kategori tidak terstandarisasi.

---

## 3. Masalah yang Diselesaikan

1. **Duplikasi Kategori Akibat Teks Bebas**: Operator dapat mengetik kategori dengan ejaan berbeda, menghasilkan data kategori yang tidak konsisten dan tidak dapat dianalisis secara andal.
2. **Tidak Ada Kontrol Daftar Kategori**: Tidak ada cara bagi admin/operator senior untuk mendefinisikan, menambah, atau menghapus kategori yang diizinkan.
3. **Tidak Ada Filter Kategori di Daftar Temuan**: Operator tidak dapat menyaring temuan berdasarkan kategori di halaman `/findings`.
4. **Sumber Data Analisis yang Tidak Andal**: Heatmap Temuan (Iterasi 07) dan statistik kategori (Iterasi 08) tidak dapat dibangun di atas data kategori yang kotor.

---

## 4. Tujuan Iterasi

1. Menyediakan tabel master kategori (`finding_categories`) sebagai sumber kebenaran tunggal untuk nama kategori.
2. Menyediakan antarmuka CRUD sederhana untuk operator mengelola daftar kategori master.
3. Mengganti sumber auto-suggest di form verifikasi dari `DISTINCT operator_category` ke tabel master.
4. Menambahkan filter kategori di halaman Daftar Temuan (`/findings`).
5. Menjaga kompatibilitas penuh — kolom `operator_category` di `activity_events` tetap berisi teks, hanya sumbernya yang divalidasi dari master.

---

## 5. Ruang Lingkup

### Opsi A — Full Scope (Memerlukan DB Migration)

**Prasyarat:** Persetujuan pembuatan tabel `finding_categories`.

#### Database (Forbidden — Perlu Persetujuan Eksplisit)
- **[NEW TABLE]** `finding_categories` — dibuat manual oleh admin, bukan via migration otomatis.
  - Kolom minimal: `id` (int, PK auto-increment), `name` (varchar 255, unique, not null), `created_at`, `updated_at`
  - Tidak ada relasi FK ke `activity_events` — `operator_category` tetap varchar, validasi dilakukan di aplikasi bukan di DB.

#### Backend
- **[NEW]** `app/Models/FindingCategory.php` — Model Eloquent untuk `finding_categories`.
- **[NEW]** `app/Http/Controllers/FindingCategoryController.php` — CRUD: `index`, `store`, `update`, `destroy`.
- **[MODIFY]** [`app/Http/Controllers/Verification/FindingController.php`](../../../app/Http/Controllers/Verification/FindingController.php) — Ganti sumber `$suggestedCategories` dari `DISTINCT operator_category` ke `FindingCategory::orderBy('name')->pluck('name')`.
- **[MODIFY]** [`app/Http/Controllers/FindingController.php`](../../../app/Http/Controllers/FindingController.php) — Tambah filter `operator_category` dari request query string.

#### Frontend
- **[NEW]** `resources/views/categories/index.blade.php` — Halaman CRUD kategori master (list + form inline).
- **[MODIFY]** [`resources/views/findings/index.blade.php`](../../../resources/views/findings/index.blade.php) — Tambah filter dropdown kategori.

#### Routing
- **[MODIFY]** [`routes/web.php`](../../../routes/web.php) — Tambah route resource `categories` untuk CRUD.

---

### Opsi B — Reduced Scope (Tanpa DB Migration)

Jika keputusan schema belum tersedia, iterasi dapat dibatasi pada:

1. **Halaman manajemen kategori berbasis file/session** — tidak praktis untuk produksi, tidak disarankan.
2. **Hanya filter kategori di `/findings`** — menggunakan `DISTINCT operator_category` yang ada, tanpa master table. Nilai bisnis terbatas tapi tidak memerlukan schema change.
3. **Defer iterasi** — tunda sampai keputusan schema tersedia.

**Rekomendasi:** Opsi A (Full Scope) dengan tabel dibuat manual oleh admin. Ini adalah pendekatan paling bersih dan sudah direncanakan sejak `roadmap_review_002.md`.

---

## 6. Yang Tidak Termasuk (Out of Scope)

1. **Migrasi data lama** — Membersihkan data `operator_category` yang sudah ada (duplikasi/typo) bukan bagian dari iterasi ini. Ini adalah pekerjaan manual oleh admin.
2. **Relasi FK database** — Kolom `operator_category` di `activity_events` tetap varchar bebas; tidak ditambahkan foreign key constraint.
3. **Heatmap Temuan** — Visualisasi heatmap berdasarkan sebaran titik temuan masuk Iterasi 07.
4. **Role-based Access Control** — Halaman CRUD kategori tidak memiliki pembatasan akses berbasis role (BL-003 belum selesai).
5. **Import/Export kategori dari CSV** — Tidak termasuk dalam scope ini.

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Tabel `finding_categories` tersedia di database (dibuat manual oleh admin).
- [ ] Model `FindingCategory` dapat membaca dan menulis ke tabel `finding_categories`.
- [ ] Halaman `/categories` menampilkan daftar kategori master yang ada.
- [ ] Operator dapat menambah kategori baru melalui form di halaman `/categories`.
- [ ] Operator dapat menghapus kategori yang tidak lagi digunakan.
- [ ] Auto-suggest di form verifikasi temuan (`/verifications/sessions/{session}/findings/review`) membaca dari tabel `finding_categories` — bukan dari `DISTINCT operator_category`.
- [ ] Filter dropdown kategori tersedia di halaman `/findings` dan berfungsi menyaring temuan.
- [ ] Nilai `operator_category` yang disimpan ke `activity_events` tetap berupa string (tidak ada perubahan pada kolom atau API mobile).
- [ ] Tidak ada error baru di log Laravel maupun console browser.
- [ ] Tidak ada perubahan pada API mobile atau kontrak sinkronisasi.

---

## 8. Risiko dan Hal yang Perlu Diperhatikan

| # | Risiko | Tingkat | Mitigasi |
|---|--------|---------|----------|
| 1 | **Schema DB belum disetujui** — Iterasi tidak dapat dimulai tanpa tabel `finding_categories` | Tinggi | Konfirmasi eksplisit dari admin/owner sebelum implementasi. |
| 2 | **Data lama tidak konsisten** — Temuan yang sudah diverifikasi memiliki `operator_category` dengan ejaan tidak standar | Sedang | Migrasi data manual oleh admin setelah tabel master tersedia. Tidak diblokir oleh iterasi ini. |
| 3 | **CRUD tanpa autentikasi** — Halaman kategori dapat diakses siapa saja selama BL-003 belum selesai | Sedang | Dokumentasikan sebagai risiko yang diterima. Mitigasi: aplikasi saat ini digunakan internal. |
| 4 | **Sumber auto-suggest berubah** — Jika operator pernah menggunakan kategori yang belum ada di master, input akan tetap tersimpan tapi tidak muncul di saran | Rendah | Informasikan kepada admin untuk seed tabel master dengan kategori yang sudah ada sebelum go-live. |

---

## 9. Dampak Terhadap Pengguna

- **Operator Verifikasi**: Auto-suggest kategori menjadi konsisten dan terpercaya — tidak ada lagi saran typo atau duplikasi. Input kategori terstandardisasi dari daftar yang sudah dikurasi.
- **Operator Monitoring**: Filter kategori di Daftar Temuan memungkinkan analisis cepat — misalnya "tampilkan semua temuan kategori Jaring Ilegal". Ini adalah fondasi untuk Heatmap Temuan (Iterasi 07).
- **Admin**: Memiliki kontrol penuh atas daftar kategori yang diizinkan dalam sistem.

---

## 10. Keputusan Yang Dibutuhkan Sebelum Implementasi

> **STOP** — Jawab pertanyaan berikut sebelum memberikan approval implementasi:

1. **Apakah pembuatan tabel `finding_categories` disetujui?**
   - Jika Ya → lanjut Full Scope (Opsi A). Admin membuat tabel manual, lalu developer membuat model dan controller.
   - Jika Tidak/Tunda → jalankan Opsi B (filter saja) atau defer iterasi.

2. **Apakah ada kategori seed awal yang harus dimasukkan ke tabel master?**
   - Disarankan: admin mengambil data `DISTINCT operator_category` yang sudah ada, membersihkan duplikasi manual, lalu seed ke tabel baru.

3. **Struktur tabel `finding_categories` yang disetujui?**
   - Minimal yang diusulkan: `id`, `name` (unique), `created_at`, `updated_at`.
   - Apakah perlu kolom tambahan (misal: `description`, `color`, `is_active`)?
