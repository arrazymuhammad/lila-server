# BACKLOG

> Daftar kebutuhan, perbaikan, technical debt, dan ide yang belum masuk roadmap aktif.
>
> Item dalam backlog dapat dipromosikan menjadi iterasi roadmap jika nilai bisnisnya meningkat atau menjadi prioritas.

---

# HIGH PRIORITY

## BL-001 — Rejected Reason Persistence

Status:
Open

Kategori:
Feature Improvement

Prioritas:
High

Latar Belakang:
Saat perjalanan ditolak oleh operator, pengguna mobile tidak mengetahui alasan penolakan secara permanen.

Nilai Bisnis:
Mempermudah proses revisi dan resubmission data lapangan.

Ketergantungan:
Mungkin memerlukan perubahan database.

Catatan:
Belum menjadi bagian roadmap aktif.

---

## BL-002 — SyncController Status Reset

Status:
Open

Kategori:
Technical Debt

Prioritas:
High

Latar Belakang:
Proses sinkronisasi saat ini selalu mengubah status menjadi submitted.

Risiko:
Data yang telah diverifikasi dapat kembali masuk ke antrian verifikasi.

Catatan:
Mungkin membutuhkan koordinasi dengan aplikasi mobile.

---

## BL-009 — Manajemen Kategori Master

Status:
Open

Kategori:
Feature — Lanjutan Iterasi 03-B

Prioritas:
High

Latar Belakang:
Iterasi 03-B mengimplementasikan input `operator_category` berbasis teks bebas dengan auto-suggest. Tanpa tabel master kategori, ada risiko duplikasi kategori akibat perbedaan ejaan. Data kategori tidak dapat dijadikan filter atau analisis yang andal.

Nilai Bisnis:
Data temuan yang terkategori dengan baik adalah fondasi dari analisis dan pelaporan LILA. Tanpa kategori yang bersih, heatmap temuan dan statistik kategori tidak dapat dibangun.

Ketergantungan:
Memerlukan keputusan schema database (tabel baru `finding_categories`).

Catatan:
Sudah masuk roadmap sebagai Iterasi 6.

---

# MEDIUM PRIORITY

## BL-006 — Iteration 3-B: Pengayaan Kategori Temuan oleh Operator

Status:
Planned

Kategori:
Feature — Lanjutan Iterasi 03-A

Prioritas:
Medium

Latar Belakang:
Saat verifikasi temuan (Iterasi 03-A), operator hanya dapat Approve/Reject. Namun data temuan dari mobile sering kali memiliki kategori yang kurang akurat (karena petugas lapangan memilih kategori secara cepat). Fitur ini memungkinkan operator memberikan atau mengubah kategori temuan saat proses verifikasi, sehingga data menjadi lebih kaya dan dapat dianalisis lebih lanjut.

Nilai Bisnis:
Meningkatkan kualitas dan kedalaman data temuan pengamatan untuk keperluan analisis dan pelaporan.

Ketergantungan:
- Iterasi 03-A harus selesai terlebih dahulu.
- Memerlukan kolom `operator_category` di tabel `activity_events` (bisa ditambahkan manual bersamaan dengan kolom `status`).
- Tumpang tindih dengan Iterasi 5 (Kategori Temuan) — evaluasi apakah perlu digabung.

Catatan:
Didefinisikan saat perencanaan Iterasi 03 pasca pre-implementation review.

---

## BL-007 — Edit Mode untuk Temuan Verified

Status:
Open

Kategori:
Feature Improvement

Prioritas:
Medium

Latar Belakang:
Saat ini tidak ada cara untuk mengoreksi `operator_category` atau data lain pada temuan yang sudah berstatus `verified` tanpa harus melakukan reject terlebih dahulu. Jika operator keliru mengisi kategori, proses koreksinya sangat tidak efisien.

Nilai Bisnis:
Meningkatkan efisiensi kerja operator dan integritas data kategori.

Prosedur Edit Mode yang Disarankan:
- Tambahkan tombol "Edit Kategori" di halaman `/findings/{event}` (khusus operator).
- Edit hanya mengizinkan perubahan `operator_category` — data asli mobile tidak berubah.
- Status temuan tetap `verified` setelah edit, tidak perlu antrian ulang.

Catatan:
Ditemukan saat QA Iterasi 03-B.

---

## BL-008 — Auto-suggest Kategori Alphabetic

Status:
Open

Kategori:
UX Improvement

Prioritas:
Low

Latar Belakang:
Daftar saran pada auto-suggest Kategori Baku saat ini muncul berdasarkan urutan database (waktu masuk), bukan urutan alphabetic. Ini membuat pencarian kategori kurang konsisten.

Nilai Bisnis:
Meningkatkan konsistensi dan kemudahan pemilihan kategori oleh operator.

Catatan:
Ditemukan saat QA Iterasi 03-B. Perbaikan minor, mudah diimplementasikan.

---

Status:
Open

Kategori:
Infrastructure

Prioritas:
Medium

Latar Belakang:
Saat ini aplikasi web belum memiliki mekanisme login dan kontrol akses.

Catatan:
Belum menjadi kebutuhan utama selama aplikasi digunakan secara internal.

---

## BL-004 — Verification Audit Trail

Status:
Open

Kategori:
Feature Improvement

Prioritas:
Medium

Latar Belakang:
Belum tersedia riwayat siapa yang melakukan verifikasi atau penolakan.

Nilai Bisnis:
Meningkatkan akuntabilitas proses verifikasi.

---

# LOW PRIORITY

## BL-005 — CSV Export

Status:
Open

Kategori:
Feature Improvement

Prioritas:
Low

Latar Belakang:
Pengguna mungkin membutuhkan ekspor data ke format CSV.

---

# DONE

## BL-006 — Iteration 3-B: Pengayaan Kategori Temuan oleh Operator

Status:
Done

Diselesaikan di:
Iterasi 03-B

Catatan:
Fitur input `operator_category` dengan auto-suggest berhasil diimplementasikan. Tabel master kategori menjadi item terpisah (BL-009).

---

# DEFERRED

(Item yang sengaja ditunda)

---

# REJECTED

(Item yang diputuskan tidak akan dikerjakan)
