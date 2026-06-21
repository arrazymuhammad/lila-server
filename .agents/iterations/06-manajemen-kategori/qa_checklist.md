# QA Checklist
## Iterasi 06 — Manajemen Kategori Master

> Dibuat berdasarkan: `iteration.md`, `implementation_report.md`, `walkthrough.md`
> URL aplikasi: http://lila.test

---

## Functional Test

### 1. Manajemen Kategori Master (CRUD)
- [x] Buka halaman `/categories`. Pastikan halaman memuat form **Tambah Kategori Baru** dan tabel daftar kategori.
- [x] Tambahkan kategori baru (misal: "Alat Tangkap Terlarang") → pastikan kategori muncul di tabel daftar kategori dengan flash message success.
- [x] Coba tambahkan kategori dengan nama yang sama persis (duplikat) → pastikan divalidasi dengan pesan error "The name has already been taken."
- [x] Tambahkan kategori lain (misal: "Pencemaran Minyak") → pastikan daftar kategori terurut secara abjad (*alphabetical*).
- [x] Klik tombol "Hapus" pada salah satu kategori → muncul konfirmasi dialog konfirmasi browser.
- [x] Setujui dialog hapus → kategori terhapus dari tabel dengan flash message success.

### 2. Auto-Suggest pada Form Verifikasi Temuan
- [x] Buka halaman Antrian Verifikasi Temuan di `/verifications/findings`.
- [x] Pilih salah satu perjalanan dan klik **Review** hingga masuk ke form verifikasi individual (`/verifications/sessions/{session}/findings/review`).
- [x] Fokus ke input field **Kategori Baku (Operator)** → pastikan dropdown suggest muncul dan hanya menyajikan daftar kategori yang terdaftar di master `/categories`.
- [x] Ketik kata kunci pencarian (misal: "Alat") → pastikan saran menyaring secara real-time berdasarkan input (menggunakan fungsi AlpineJS).
- [x] Pilih salah satu saran kategori, isi data lain, lalu klik **Verifikasi (Approve)**.
- [x] Pastikan data tersimpan dengan benar di database (cek di halaman detail temuan `/findings/{event}` kategori yang bersangkutan bernilai sesuai input).
- [x] Hapus kategori tersebut dari master di `/categories` → kembali ke form verifikasi temuan lain, pastikan kategori yang telah dihapus tidak lagi muncul di dalam dropdown auto-suggest.

### 3. Filter Kategori pada Daftar Temuan
- [x] Buka halaman Daftar Temuan di `/findings`.
- [x] Pastikan terdapat dropdown filter baru bertema **Kategori** yang berisi semua kategori dari master.
- [x] Pilih salah satu kategori filter (misal: "Alat Tangkap Terlarang") dan klik **Terapkan** → pastikan daftar temuan tersaring hanya menampilkan temuan dengan kategori baku yang dicari.
- [x] Pilih "Semua kategori" dan klik **Terapkan** atau klik tombol **Reset** → pastikan filter ter-reset dan menampilkan seluruh daftar temuan.
- [x] Kombinasikan filter Kategori dengan filter lain (pencarian kata kunci `q`, tanggal `date`, atau `session_id`) → pastikan query pencarian bekerja beriringan secara benar.

---

## UI Test
- [x] Layout halaman `/categories` terlihat bersih, menggunakan warna indigo yang konsisten dengan sistem verifikasi LILA.
- [x] Penempatan dropdown Kategori di `/findings` terlihat presisi dan sejajar di dalam grid filter bersama filter pencarian, tanggal, dan perjalanan.
- [x] Autocomplete dropdown di form review verifikasi tampil mengambang (*absolute positioning* dengan *z-index* memadai) dan tidak menggeser input field di bawahnya saat terbuka.
- [x] Dialog konfirmasi hapus kategori menggunakan kalimat peringatan yang jelas dan informatif.
- [x] Flash message notifikasi (sukses simpan/hapus) tampil dengan warna hijau kontras yang mudah dibaca.

---

## Regression Test
- [x] Detail Temuan (`/findings/{event}`) dapat diakses tanpa error 500 dan menampilkan Kategori Baku (Operator) berlabel badge ungu dengan benar jika ada, atau teks italic abu-abu "Belum dikategorikan" jika kosong.
- [x] API Sinkronisasi Mobile (`/api/sync`) berfungsi normal — pastikan tidak ada kegagalan sinkronisasi data karena skema `activity_events` tidak diubah relasinya secara fisik (foreign key).
- [x] Proses verifikasi perjalanan secara keseluruhan di `/verifications` tetap bekerja normal tanpa error.
- [x] Halaman Peta `/map` dan `/maps` dapat dimuat tanpa kendala, dan filter bulan/tahun serta toggle heatmap tetap berfungsi normal.
- [x] Halaman Dashboard `/dashboard` dapat diakses tanpa error 500.

---

## Acceptance Criteria
- [x] Menyediakan tabel database `finding_categories` sebagai master data kategori baku.
- [x] Antarmuka CRUD kategori master berfungsi penuh di `/categories` (Tambah dan Hapus).
- [x] Validasi keunikan nama kategori (prevent duplikasi) berfungsi.
- [x] Logika saran auto-suggest pada form verifikasi temuan memuat daftar dari tabel master.
- [x] Filter berdasarkan kategori tersedia dan berfungsi secara akurat di `/findings`.
- [x] Integritas data existing terjaga (penghapusan kategori master tidak merusak data `operator_category` string yang sudah disimpan di `activity_events`).
- [x] Kompatibilitas API mobile 100% aman tanpa perubahan skema API / contract payload.

---

## Notes
1. **Tidak ada migrasi otomatis** — Tabel `finding_categories` diasumsikan sudah dibuat secara manual oleh admin/database setup sebelum pengetesan.
2. **Kategori yang dihapus tetap tersimpan di temuan lama** — Ini adalah perilaku terdesain (*non-destructive*) demi menjaga integritas data riwayat verifikasi terdahulu.
3. **Data uji awal** — Disarankan menambahkan minimal 3 kategori master di `/categories` sebelum menguji halaman review verifikasi dan filter daftar temuan.
4. sidebar menu belum ada untuk kategori
5. pesan duplikat dalam bahasa indonesia
6. perlu headmap berdasarkan kategori. headmap berdasarkan trackpoint kurang relevan
