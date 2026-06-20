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
