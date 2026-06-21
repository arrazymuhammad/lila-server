# QA Report: Iterasi 03 (03-A & 03-B) — Verifikasi & Pengayaan Temuan Pengamatan

## Status
**PASS WITH NOTES**

---

## Ringkasan Pengujian

Pengujian manual untuk Iterasi 03-A (Verifikasi Temuan) dan Iterasi 03-B (Pengayaan Kategori) telah diselesaikan dalam satu siklus QA terpadu. Secara keseluruhan, semua fitur inti berjalan sesuai spesifikasi. Ditemukan satu bug (sudah diperbaiki), dua item yang ditunda sebagai fitur berikutnya, dan tiga item yang belum bisa diuji karena memerlukan akses Postman.

---

## Hasil Checklist

### Iterasi 03-A — Verifikasi Temuan Pengamatan

| Area | Hasil |
|------|-------|
| Functional Test | ✅ 8/8 Lulus |
| UI Test | ✅ 5/5 Lulus |
| Visibility Rule Test | ✅ 5/5 Lulus |
| SyncController Test | ⏳ 2/2 Ditunda (TO BE TESTED) |
| Regression Test | ✅ 4/4 Lulus |
| Acceptance Criteria | ✅ 10/10 Lulus |

### Iterasi 03-B — Pengayaan Kategori Temuan

| Area | Hasil |
|------|-------|
| Functional Test | ✅ 6/8 Lulus, 1 Bug Fixed, 1 Next Feature |
| UI Test | ✅ 5/5 Lulus |
| Visibility & Data Integrity | ✅ 2/3 Lulus, 1 Ditunda (TO BE TESTED) |
| Auto-suggest Quality | ✅ 3/3 Lulus |
| Regression Test | ✅ 4/4 Lulus |
| Acceptance Criteria | ✅ 6/8 Lulus, 1 Bug Fixed, 1 Next Feature |

---

## Bug Yang Ditemukan

### BUG-01 — Field Kategori Baku Semula Bersifat Wajib *(Sudah Diperbaiki)*
**Ditemukan di:** Iterasi 03-B, Functional Test & Acceptance Criteria
**Deskripsi:** Ketika field Kategori Baku dibiarkan kosong dan operator mengklik Approve, sistem menolak proses karena field dianggap wajib diisi. Padahal berdasarkan spesifikasi awal, field ini bersifat opsional.
**Status:** `[*BUG FIXED*]` — sudah diperbaiki selama siklus QA berlangsung.

---

## Catatan QA

### 1. Edit Mode untuk Temuan yang Sudah Diverifikasi (Kebutuhan Baru)
Tester mencatat bahwa Mode Review saat ini bersifat **one-way**: operator hanya bisa Approve atau Reject, tidak ada kemampuan untuk menyunting data temuan yang sudah disetujui. Jika operator keliru (misalnya mengisi kategori yang salah), tidak ada cara untuk mengoreksinya tanpa menolak temuan terlebih dahulu.

**Gambaran prosedur Edit Mode yang disarankan untuk iterasi berikutnya:**
- Dari halaman Detail Temuan (`/findings/{event}`), tambahkan tombol "Edit Kategori Baku" yang khusus dapat diakses oleh operator.
- Edit mode hanya mengizinkan perubahan pada `operator_category` — tidak mengubah data asli dari mobile (`title`, `description`, `latitude`, `longitude`).
- Perubahan dicatat dengan indikator "Terakhir diperbarui oleh Operator" tanpa perlu audit trail penuh.
- Status temuan tetap `verified` setelah edit — tidak perlu melalui antrian verifikasi ulang.

### 2. Urutan Alphabetic pada Auto-suggest Kategori
Tester menyarankan agar daftar saran pada auto-suggest Kategori Baku diurutkan secara **alphabetic** untuk memudahkan pencarian dan konsistensi pemilihan. Saat ini urutan saran mengikuti urutan data di database (berdasarkan waktu masuk).

### 3. Risiko Duplikasi Kategori
Karena input berupa teks bebas, ada risiko inkonsistensi ejaan ("Sampah Plastik" vs "sampah plastik" vs "sampah"). Ini adalah masalah data quality yang perlu diselesaikan melalui **manajemen kategori master** di iterasi berikutnya.

---

## Risiko Yang Masih Ada

| # | Risiko | Tingkat | Keterangan |
|---|--------|---------|------------|
| 1 | **Duplikasi kategori** | Tinggi | Input teks bebas tanpa normalisasi — perlu tabel kategori master |
| 2 | **Tidak ada Edit Mode** | Sedang | Temuan verified tidak bisa dikoreksi tanpa reject-ulang |
| 3 | **Override protection belum diuji penuh** | Sedang | SyncController belum diuji dengan Postman — perlu dikonfirmasi |
| 4 | **Antrian awal sangat panjang** | Sedang | Semua temuan lama berstatus submitted — butuh waktu untuk diverifikasi |
| 5 | **Tidak ada audit trail penolakan** | Rendah | Alasan reject temuan tidak disimpan permanen (sesuai BL-001) |
| 6 | **Auto-suggest tidak alphabetic** | Rendah | UX minor — mudah diperbaiki di iterasi berikutnya |
