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
Planned (Iteration 6)

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

# HIGH PRIORITY

## BL-012 — Peta Interaktif: Rich Finding Popup

Status:
Planned (Iteration 08)

Kategori:
Feature Improvement

Prioritas:
High

Latar Belakang:
Saat ini, marker temuan di `/map` hanya menampilkan status dan link ke halaman detail. Operator sering harus melakukan banyak klik (*open in new tab*) untuk sekadar melihat foto atau kategori temuan.

Nilai Bisnis:
Mempercepat proses investigasi dan overview lapangan oleh operator langsung dari peta utama.

Prosedur Edit Mode yang Disarankan:
- `MapController` mengekspos foto, timestamp, deskripsi, dan kategori.
- Popup marker menampilkan UI card kecil.
- Tersedia fitur carousel mini jika foto > 1.

Catatan:
Di-request oleh QA saat testing Iterasi 07. Langsung dipromosikan ke Iterasi 08.

---

# MEDIUM PRIORITY

## BL-007 — Edit Mode untuk Temuan Verified

Status:
Open

Kategori:
Feature Improvement

Prioritas:
Medium-High

Latar Belakang:
Saat ini tidak ada cara untuk mengoreksi `operator_category` atau data lain pada temuan yang sudah berstatus `verified` tanpa harus melakukan reject terlebih dahulu. Jika operator keliru mengisi kategori, proses koreksinya sangat tidak efisien.

Nilai Bisnis:
Meningkatkan efisiensi kerja operator dan integritas data kategori.

Prosedur Edit Mode yang Disarankan:
- Tambahkan tombol "Edit Kategori" di halaman `/findings/{event}` (khusus operator).
- Edit hanya mengizinkan perubahan `operator_category` — data asli mobile tidak berubah.
- Status temuan tetap `verified` setelah edit, tidak perlu antrian ulang.

Catatan:
Ditemukan saat QA Iterasi 03-B. Urgensi meningkat pasca Iterasi 06 — dengan master kategori tersedia, operator kini lebih sering butuh koreksi kategori temuan lama. Kandidat Iterasi 08.

---

## BL-008 — Auto-suggest Kategori Alphabetic

Status:
Resolved

Kategori:
UX Improvement

Prioritas:
Low

Latar Belakang:
Daftar saran pada auto-suggest Kategori Baku saat ini muncul berdasarkan urutan database (waktu masuk), bukan urutan alphabetic. Ini membuat pencarian kategori kurang konsisten.

Nilai Bisnis:
Meningkatkan konsistensi dan kemudahan pemilihan kategori oleh operator.

Catatan:
Ditemukan saat QA Iterasi 03-B. Diselesaikan otomatis oleh Iterasi 06 — auto-suggest kini membaca dari `finding_categories` yang di-query dengan `orderBy('name')`.

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

## BL-010 — Evaluasi Formula & Relevansi Progress Bar

Status:
Open

Kategori:
UX / Metric Review

Prioritas:
Low

Latar Belakang:
Iterasi 04 mengubah formula progress bar di Daftar Perjalanan dengan bobot (temuan ×10, foto ×5, trackpoint ×1). Perlu dievaluasi kegunaan jangka panjangnya bagi operator, atau apakah perlu diganti dengan metrik/indikator visual lain yang lebih intuitif pada Iterasi 08 (Pelaporan & Statistik Lanjutan).

Nilai Bisnis:
Memastikan informasi visual yang disajikan di Daftar Perjalanan benar-benar merepresentasikan keaktifan patroli yang berguna untuk analisis.

---

# DONE

## BL-003 — Authentication & Access Control

Status:
Done

Diselesaikan di:
Iterasi 09

Catatan:
Aplikasi web kini diproteksi menggunakan sistem autentikasi. Halaman dashboard, peta, dan verifikasi tertutup bagi publik.

---

## BL-006 — Iteration 3-B: Pengayaan Kategori Temuan oleh Operator

Status:
Done

Diselesaikan di:
Iterasi 03-B

Catatan:
Fitur input `operator_category` dengan auto-suggest berhasil diimplementasikan. Tabel master kategori menjadi item terpisah (BL-009).

---

## BL-008 — Auto-suggest Kategori Alphabetic

Status:
Resolved (otomatis oleh Iterasi 06)

Catatan:
Auto-suggest membaca dari `finding_categories` yang sudah alpha-sorted. Tidak diperlukan implementasi terpisah.

---

## BL-009 — Manajemen Kategori Master

Status:
Done

Diselesaikan di:
Iterasi 06

Catatan:
Tabel `finding_categories`, CRUD `/categories`, integrasi auto-suggest verifikasi, dan filter temuan berhasil diimplementasikan.

---

## BL-011 — Normalisasi Kategori Temuan Historis

Status:
Open

Kategori:
Data Maintenance

Prioritas:
Low

Latar Belakang:
Data `operator_category` yang diisi sebelum Iterasi 06 (saat masih free text) mungkin tidak cocok secara persis dengan nama kategori di tabel master baru. Sebuah proses one-time data cleanup atau alat admin sederhana mungkin berguna untuk menjamin konsistensi data historis dengan master.

Nilai Bisnis:
Meningkatkan kualitas data untuk analisis kategori jangka panjang (terutama heatmap temuan berdasarkan kategori).

Catatan:
Ditambahkan dari review pasca Iterasi 06. Bukan kebutuhan mendesak — heatmap dan filter sudah berfungsi dengan string comparison. Kandidat fitur admin jangka panjang.

---

# DEFERRED

(Item yang sengaja ditunda)

---

# REJECTED

(Item yang diputuskan tidak akan dikerjakan)
