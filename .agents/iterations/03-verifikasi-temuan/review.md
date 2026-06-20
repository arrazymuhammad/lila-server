# PRE-IMPLEMENTATION REVIEW
## Iterasi 03 — Verifikasi Temuan Pengamatan

> Tanggal Review: 2026-06-21
> Reviewer: Technical Reviewer
> Status: **PERLU KEPUTUSAN — ada blocker kritis sebelum implementasi dapat dimulai**

---

## 1. Apakah Ruang Lingkup Sudah Jelas?

**Sebagian besar ya, dengan satu pengecualian kritis.**

Ruang lingkup telah mendefinisikan dengan baik:
- Pembuatan halaman antrian verifikasi temuan (`/verifications/findings`).
- Pembaruan Visibility Rule di `FindingController` dan `MapController`.
- Penambahan navigasi sidebar.
- Pola implementasi mengikuti Iterasi 01-02.

**Yang belum jelas dan menjadi blocker:**
Iterasi mengakui bahwa `activity_events` tidak memiliki kolom `status`, dan meminta tim teknis untuk mengaudit apakah ada solusi tanpa migration. **Audit ini sudah dilakukan dan hasilnya adalah:**

> ❌ **Tidak ada kolom yang dapat dimanfaatkan tanpa risiko.**

Detail temuan audit:

```
Model ActivityEvent — Kolom yang Tersedia:
├── id          (UUID, primary key dari mobile)
├── session_id  (relasi ke tracking_sessions)
├── title       (kategori numerik dari mobile — KRITIS, tidak boleh diubah)
├── description (isi deskripsi temuan — tidak boleh dipakai sebagai status)
├── latitude    (koordinat — tidak relevan)
├── longitude   (koordinat — tidak relevan)
└── timestamp   (waktu temuan — tidak relevan)
```

Tidak ada satu pun kolom yang merupakan kandidat aman untuk menyimpan status verifikasi. Menggunakan `title` atau `description` sebagai status akan merusak integritas data dan kompatibilitas dengan mobile.

**Kesimpulan:** Iterasi 03 memerlukan penambahan kolom baru (`status` atau `verification_status`) pada tabel `activity_events` melalui migration database.

---

## 2. Apakah Ada Konflik dengan Sistem Saat Ini?

### ✅ Tidak Ada Konflik pada Area yang Bisa Dikerjakan

**`FindingController`** — Saat ini sudah menggunakan `whereHas('session', fn($q) => $q->where('status', 'verified'))`. Sudah ada pola yang bisa diperluas untuk filter status temuan.

**`MapController`** — Event di-load melalui relasi `session->events` tanpa filter status. Jika kolom status tersedia, filter bisa ditambahkan di sini.

**`VerificationController`** — Saat ini khusus untuk `TrackingSession`. Dapat diperluas dengan method/route baru untuk `ActivityEvent` tanpa mengubah fungsi yang sudah ada.

**`routes/web.php`** — Tersedia 23 baris, masih ringkas. Penambahan route baru tidak berisiko.

### ⚠️ Konflik Potensial

**MapController baris 46-55:** Temuan di peta di-load via `$session->events` (semua events dari session verified). Jika Visibility Rule diterapkan di level temuan, map harus memfilter `events` yang `verified` dari collection yang sudah di-load. Ini **tidak bisa dilakukan di level query** karena events di-load eager loading tanpa kondisi status — karena kolom status belum ada. Setelah migration, perlu diperbarui agar hanya events `verified` yang masuk ke array `findings`.

---

## 3. Apakah Ada Risiko yang Belum Disebutkan?

### Risiko Baru yang Ditemukan dari Audit Kode

**Risiko A — Volume antrian awal sangat besar:**
`ActivityEvent` berisi semua temuan dari seluruh perjalanan yang masuk, termasuk semua historical data. Jika Visibility Rule diterapkan setelah kolom status ditambahkan, semua temuan lama akan berstatus `null` (bukan `verified`), sehingga **seluruh Daftar Temuan dan Peta akan kosong** sampai operator memverifikasi ribuan temuan satu per satu. Diperlukan **strategi migrasi data** (misalnya: set status default ke `verified` untuk semua temuan yang berasal dari perjalanan `verified`).

**Risiko B — SyncController tidak akan mengisi kolom status baru:**
Berdasarkan audit `SyncController` sebelumnya, proses import events menggunakan `ActivityEvent::updateOrCreate()`. Kolom baru tidak akan diisi secara otomatis kecuali ada modifikasi di SyncController. Artinya, setiap temuan baru dari mobile akan memiliki `status = null`, bukan `submitted`.

> ⚠️ **Ini berarti kita perlu memodifikasi `SyncController`** — yang secara teknis menyentuh API layer, meskipun tidak mengubah kontrak API mobile (hanya menambahkan logika internal). Perlu konfirmasi apakah ini diperbolehkan dalam batasan `AI_CONTEXT.md`.

---

## 4. Estimasi Jumlah File yang Berubah

Jika migration diperbolehkan:

| # | File | Jenis Perubahan |
|---|------|-----------------|
| 1 | `database/migrations/xxxx_add_status_to_activity_events.php` | **[BARU]** Migration |
| 2 | `app/Models/ActivityEvent.php` | **[MODIFY]** Tambah `status` di fillable & cast |
| 3 | `app/Http/Controllers/VerificationController.php` | **[MODIFY]** Tambah method untuk findings |
| 4 | `app/Http/Controllers/FindingController.php` | **[MODIFY]** Update query dengan filter status |
| 5 | `app/Http/Controllers/MapController.php` | **[MODIFY]** Filter events verified di peta |
| 6 | `app/Http/Controllers/Api/SyncController.php` | **[MODIFY]** Set status default saat import events |
| 7 | `routes/web.php` | **[MODIFY]** Tambah route verifikasi temuan |
| 8 | `resources/views/verifications/findings.blade.php` | **[BARU]** View tabel antrian temuan |
| 9 | `resources/views/layouts/app.blade.php` | **[MODIFY]** Update sidebar navigasi |

**Total: 9 file** — Melebihi ambang batas 5 file dari `implementation_prompt.md`.

> ⚠️ Perlu dipecah menjadi task yang lebih kecil saat implementasi.

---

## 5. Area Kode yang Kemungkinan Terdampak

| Area | Dampak | Level |
|------|--------|-------|
| `ActivityEvent` model | Perlu update fillable | Rendah |
| `FindingController` | Tambah filter status findings | Rendah |
| `MapController` | Filter events di collection findings | Rendah |
| `VerificationController` | Extend untuk findings | Sedang |
| `SyncController` | Set default status events | Sedang — perlu perhatian khusus |
| Migration & Schema | Penambahan kolom | **Tinggi — perlu keputusan Product Owner** |

---

## Kesimpulan Review

**Implementasi TIDAK DAPAT dimulai** tanpa resolusi dari dua keputusan berikut:

### Keputusan 1 (WAJIB) — Izin Migration Database
> Apakah penambahan kolom `status` (atau `verification_status`) pada tabel `activity_events` diizinkan?
>
> Tanpa ini, Iterasi 03 tidak dapat diimplementasikan sama sekali.

### Keputusan 2 (WAJIB) — Izin Modifikasi SyncController
> Apakah SyncController (`app/Http/Controllers/Api/SyncController.php`) boleh dimodifikasi untuk mengisi nilai default status pada events baru?
>
> Jika tidak, semua temuan baru dari mobile akan langsung masuk antrian verifikasi (status `null`/`submitted`), yang merupakan perilaku yang diinginkan — tetapi harus dikonfirmasi.

### Keputusan 3 (PENTING) — Strategi Data Lama
> Bagaimana perlakuan terhadap temuan yang sudah ada di database sebelum Iterasi 03 diimplementasikan?
>
> **Opsi A:** Semua temuan lama otomatis `verified` (tidak perlu diverifikasi ulang).
> **Opsi B:** Semua temuan lama `submitted` (harus diverifikasi ulang — antrian sangat panjang).
> **Opsi C:** Temuan lama yang berasal dari perjalanan `verified` otomatis `verified`, sisanya `submitted`.
