# Infrastructure Requirements
## Iterasi 06 — Manajemen Kategori Master

> Dokumen ini ditujukan kepada **Admin Database**.
> Tindakan berikut harus diselesaikan **sebelum** developer memulai implementasi.

---

## Tindakan Yang Diperlukan

### 1. Buat Tabel `finding_categories`

Jalankan SQL berikut secara manual di database produksi/development:

```sql
CREATE TABLE finding_categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY finding_categories_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Seed Data Kategori Awal (Opsional tapi Disarankan)

Sebelum go-live, seed tabel dengan kategori yang sudah ada di sistem. Ambil data existing:

```sql
-- Lihat kategori yang sudah ada (untuk referensi cleaning)
SELECT DISTINCT operator_category, COUNT(*) as usage_count
FROM activity_events
WHERE operator_category IS NOT NULL AND operator_category != ''
GROUP BY operator_category
ORDER BY operator_category;
```

Setelah membersihkan duplikasi/typo secara manual, seed ke tabel baru:

```sql
-- Contoh seed (sesuaikan dengan hasil cleaning)
INSERT INTO finding_categories (name, created_at, updated_at) VALUES
('Jaring Ilegal', NOW(), NOW()),
('Pencemaran Air', NOW(), NOW()),
('Aktivitas Mencurigakan', NOW(), NOW());
-- ... tambahkan sesuai daftar kategori yang sudah dibersihkan
```

---

## Konfirmasi Checklist (untuk Admin)

- [x] Tabel `finding_categories` berhasil dibuat di database **development**
- [x] Tabel `finding_categories` berhasil dibuat di database **production** (jika berbeda)
- [x] Data seed kategori awal sudah dimasukkan (opsional — bisa dilakukan setelah go-live)
- [x] Developer sudah dinotifikasi bahwa tabel tersedia

---

## Catatan

- Tabel ini **tidak memiliki relasi FK** ke tabel `activity_events`. Kolom `operator_category` di `activity_events` tetap `VARCHAR` bebas — integritas dijaga di level aplikasi, bukan database constraint.
- Data `operator_category` yang sudah ada di `activity_events` **tidak perlu diubah** secara otomatis. Nilai lama tetap tersimpan apa adanya.
- Tidak ada migration file Laravel yang dibuat untuk tabel ini — sesuai keputusan yang sudah disetujui.
