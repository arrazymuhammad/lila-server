# Backlog Review — Pasca Iterasi 06

> Dibuat: Hari ini
> Konteks: Review backlog pasca penyelesaian Iterasi 06 (Manajemen Kategori Master) dan penyusunan `roadmap_review_004.md`.

---

## 1. Status Backlog Saat Ini

| ID | Nama Backlog | Kategori | Prioritas | Status | Catatan Review |
|----|---|---|---|---|---|
| **BL-001** | Rejected Reason Persistence | Feature Improvement | High | Open | Tetap *Open* — memerlukan perubahan schema database. Ditangguhkan sampai ada persetujuan migrasi. |
| **BL-002** | SyncController Status Reset | Technical Debt | High | Open | Tetap *Open* — memerlukan koordinasi API mobile. Ditangguhkan demi stabilitas mobile. |
| **BL-003** | Authentication & Access Control | Infrastructure | Medium | Open | Tetap *Open* — belum menjadi kebutuhan utama selama aplikasi digunakan internal. |
| **BL-004** | Verification Audit Trail | Feature Improvement | Medium | Open | Tetap *Open* — belum mendesak. |
| **BL-005** | CSV Export | Feature Improvement | Low | Open | Tetap *Open* — prioritas rendah. |
| **BL-006** | Pengayaan Kategori Temuan | Feature | — | **Done** | Selesai di Iterasi 03-B. |
| **BL-007** | Edit Mode untuk Temuan Verified | Feature Improvement | **Medium-High** | Open | Urgensi **naik** pasca Iterasi 06: operator sekarang akan lebih sering perlu mengoreksi kategori temuan lama yang diisi saat tabel master belum ada. Kandidat Iterasi 08. |
| **BL-008** | Auto-suggest Kategori Alphabetic | UX Improvement | Low | **Resolved** | Diselesaikan otomatis oleh Iterasi 06 — auto-suggest kini membaca dari tabel `finding_categories` yang sudah alpha-sorted by default. |
| **BL-009** | Manajemen Kategori Master | Feature | High | **Done** | Selesai di Iterasi 06. |
| **BL-010** | Evaluasi Formula & Relevansi Progress Bar | UX / Metric Review | Low | Open | Tetap *Open* — kandidat evaluasi di Iterasi 08 (Pelaporan & Statistik). |

---

## 2. Item Baru Yang Diusulkan

Dari catatan QA Iterasi 06:

- **BL-011 — Normalisasi Kategori Temuan Historis** (Opsional)
  - Kategori: Data Maintenance
  - Prioritas: Low
  - Latar Belakang: Data `operator_category` yang diisi sebelum Iterasi 06 (saat masih *free text*) mungkin tidak cocok secara persis dengan nama kategori di tabel master baru. Sebuah proses one-time data cleanup atau alat admin sederhana mungkin berguna.
  - Catatan: Bukan kebutuhan mendesak — heatmap dan filter sudah berfungsi dengan string comparison. Diusulkan sebagai kandidat fitur admin jangka panjang.

---

## 3. Rekomendasi Tindakan (Maintenance)

1. **Update `BACKLOG.md`**:
   - Tandai BL-008 sebagai `Resolved`.
   - Tandai BL-009 sebagai `Done`.
   - Update prioritas BL-007 menjadi `Medium-High`.
   - Tambahkan BL-011 sebagai item baru (Low priority).

2. **Update `ROADMAP.md`**:
   - Status Iteration 6 diperbarui menjadi `Completed ✅`. (Sudah dilakukan saat QA sprint close.)
   - Status Iteration 7 diperbarui menjadi `Target 🔜`.
