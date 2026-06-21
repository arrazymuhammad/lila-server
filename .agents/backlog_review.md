# Backlog Review — Pasca Iterasi 05

> Dibuat: 2026-06-21
> Konteks: Review backlog pasca penyelesaian Iterasi 05 dan penyusunan `roadmap_review_004.md`.

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
| **BL-007** | Edit Mode untuk Temuan Verified | Feature Improvement | Medium | Open | Tetap *Open*. Relevansinya meningkat setelah Manajemen Kategori Master (Iterasi 06) selesai — operator akan lebih sering perlu mengoreksi kategori temuan lama. Disarankan naik ke prioritas Medium-High pasca Iterasi 06. |
| **BL-008** | Auto-suggest Kategori Alphabetic | UX Improvement | Low | Open | Tetap *Open*. Akan otomatis terselesaikan atau menjadi tidak relevan setelah Manajemen Kategori Master (Iterasi 06) — karena sumber auto-suggest akan berubah dari DISTINCT query ke tabel master. |
| **BL-009** | Manajemen Kategori Master | Feature | High | **Planned (Iteration 6)** | Audit kode mengkonfirmasi urgensi — data `operator_category` terus bertambah sebagai teks bebas tanpa validasi master. Semakin lama ditunda, semakin banyak data kotor yang perlu dibersihkan. |
| **BL-010** | Evaluasi Formula & Relevansi Progress Bar | UX / Metric Review | Low | Open | Tetap *Open* — kandidat evaluasi di Iterasi 08 (Pelaporan & Statistik). |

---

## 2. Item Baru Yang Diusulkan

Tidak ada item baru dari catatan Iterasi 05. Iterasi 05 adalah implementasi frontend murni tanpa temuan isu bisnis baru.

---

## 3. Dampak Iterasi 06 Terhadap Backlog

Iterasi 06 (Manajemen Kategori Master) diperkirakan akan berdampak pada:

- **BL-008 (Auto-suggest Alphabetic)** → Kemungkinan **Resolved** otomatis: setelah tabel master kategori tersedia, auto-suggest akan membaca dari tabel master (yang sudah diurutkan alphabetically dari awal) — bukan dari DISTINCT query yang urutannya tidak deterministik.
- **BL-007 (Edit Mode Temuan Verified)** → Urgensinya meningkat: dengan kategori master yang bersih, operator akan lebih sering perlu mengoreksi kategori temuan lama yang diisi saat tabel master belum ada. Perlu dipertimbangkan untuk dipromosikan ke Iterasi 07 atau 07-B.

---

## 4. Rekomendasi Tindakan (Maintenance)

1. **Update `ROADMAP.md`**:
   - Ubah status **Iteration 5: Heatmap Perjalanan** menjadi `Completed ✅`.
   - Ubah status **Iteration 6: Manajemen Kategori Master** menjadi `Target 🔜`.

2. **Update `BACKLOG.md`**:
   - Tidak ada perubahan status item yang diperlukan (semua sudah up-to-date).
   - Tambahkan catatan pada BL-007 bahwa urgensinya meningkat pasca Iterasi 06.
   - Tambahkan catatan pada BL-008 bahwa kemungkinan akan resolved otomatis oleh Iterasi 06.
