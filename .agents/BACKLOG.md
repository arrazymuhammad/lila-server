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

## BL-003 — Authentication & Authorization

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

(Pindahkan item ke sini setelah selesai diimplementasikan)

---

# DEFERRED

(Item yang sengaja ditunda)

---

# REJECTED

(Item yang diputuskan tidak akan dikerjakan)
