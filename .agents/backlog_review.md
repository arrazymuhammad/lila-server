# Backlog Review — Pasca Iterasi 04

> Dibuat: 2026-06-21
> Konteks: Review backlog pasca penyelesaian Iterasi 04 dan penyusunan `roadmap_review_003.md`.

---

## 1. Status Backlog Saat Ini

Berikut adalah ringkasan status item backlog saat ini sebelum pemeliharaan dilakukan:

| ID | Nama Backlog | Kategori | Prioritas | Status Aktif | Catatan Review |
|---|---|---|---|---|---|
| **BL-001** | Rejected Reason Persistence | Feature Improvement | High | Open | Tetap *Open* karena membutuhkan perubahan skema database (kolom `rejected_reason`). Ditangguhkan sampai ada persetujuan migrasi. |
| **BL-002** | SyncController Status Reset | Technical Debt | High | Open | Tetap *Open* karena membutuhkan koordinasi dengan aplikasi mobile (perubahan API contract). Ditangguhkan demi stabilitas mobile. |
| **BL-003** | Authentication & Access Control | Infrastructure | Medium | Open | Tetap *Open*. (Catatan: Header nama hilang di `BACKLOG.md` lama, perlu diperbaiki saat maintenance). |
| **BL-004** | Verification Audit Trail | Feature Improvement | Medium | Open | Tetap *Open*. Belum mendesak untuk diimplementasikan. |
| **BL-005** | CSV Export | Feature Improvement | Low | Open | Tetap *Open*. Prioritas rendah. |
| **BL-006** | Pengayaan Kategori Temuan | Feature | Medium | **Done** | Sudah diselesaikan di Iterasi 03-B. Perlu dipindahkan sepenuhnya ke section **DONE** di `BACKLOG.md` dan dihapus dari daftar prioritas aktif. |
| **BL-007** | Edit Mode untuk Temuan Verified | Feature Improvement | Medium | Open | Tetap *Open*. Diusulkan untuk tetap berada di Backlog karena prioritas pengerjaan masih di bawah Heatmap Perjalanan. |
| **BL-008** | Auto-suggest Kategori Alphabetic | UX Improvement | Low | Open | Tetap *Open*. Peningkatan UX minor. |
| **BL-009** | Manajemen Kategori Master | Feature | High | Planned | Naik ke status *Planned* karena dijadwalkan langsung sebagai **Iterasi 6** di roadmap aktif. |

---

## 2. Item Baru yang Diusulkan

Berdasarkan temuan dan catatan pasca-implementasi Iterasi 04 (`sprint_review.md` & `final_report.md`), diusulkan 1 item backlog baru:

### BL-010 — Evaluasi Formula & Relevansi Progress Bar
*   **Kategori**: UX / Metric Review
*   **Prioritas**: Low
*   **Latar Belakang**: Iterasi 04 mengubah formula progress bar di Daftar Perjalanan dengan bobot (temuan ×10, foto ×5, trackpoint ×1). Perlu dievaluasi kegunaan jangka panjangnya bagi operator, atau apakah perlu diganti dengan metrik/indikator visual lain yang lebih intuitif pada Iterasi 08 (Pelaporan & Statistik Lanjutan).
*   **Nilai Bisnis**: Memastikan informasi visual yang disajikan di Daftar Perjalanan benar-benar merepresentasikan keaktifan patroli yang berguna untuk analisis.

---

## 3. Rekomendasi Tindakan (Maintenance)

Berikut adalah rekomendasi perubahan yang akan diterapkan pada `BACKLOG.md` dan `ROADMAP.md`:
1.  **Update `ROADMAP.md`**:
    *   Ubah status **Iteration 4: Reorientasi UI Observation-Centric** menjadi `Completed ✅`.
    *   Ubah status **Iteration 5: Heatmap Perjalanan** menjadi `In Progress 🔄` atau `Planned/Target 🔜` untuk persiapan.
2.  **Update `BACKLOG.md`**:
    *   Perbaiki penulisan header `## BL-003 — Authentication & Access Control` yang hilang.
    *   Hapus duplikasi/entri aktif **BL-006** di bagian Medium Priority (karena statusnya sudah *Done*). Pastikan entri di bagian **DONE** sudah tercatat dengan benar.
    *   Tambahkan **BL-010 — Evaluasi Formula & Relevansi Progress Bar** di bagian Low Priority.
    *   Ubah status **BL-009** dari *Open* menjadi *Planned (Iteration 6)*.
