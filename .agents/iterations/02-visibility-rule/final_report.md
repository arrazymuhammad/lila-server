# FINAL REPORT: Iterasi 02 - Visibility Rule & Sentralisasi Verifikasi

## 1. Ringkasan Iterasi

Iterasi 02 berhasil menyelesaikan dua objective utama secara bersamaan: penerapan *Visibility Rule* di seluruh modul publik dan pemindahan fungsi verifikasi ke halaman antrian terpusat. Sistem kini mampu memisahkan data mentah (`submitted`) dari data sah (`verified`) secara konsisten di setiap sudut aplikasi — Dashboard, Peta, Daftar Perjalanan, dan Daftar Temuan. Seluruh Acceptance Criteria terpenuhi tanpa ditemukan bug teknis. Catatan QA bersifat desain/UX yang menjadi bekal penting untuk iterasi berikutnya.

---

## 2. Tujuan Yang Berhasil Dicapai

- [x] **Visibility Rule Backend** — Query di `DashboardController`, `MapController`, `FindingController`, dan `ActivityController` secara eksklusif hanya memuat data `verified`.
- [x] **Peta Bersih** — `/map` hanya merender rute dari sesi yang `verified`.
- [x] **Daftar Temuan Bersih** — `/findings` hanya menampilkan temuan dari sesi `verified`.
- [x] **Halaman Daftar Perjalanan Disederhanakan** — Filter status dihapus dari UI; `/activities` menjadi tampilan data sah yang bersih.
- [x] **Halaman Antrian Verifikasi** — Halaman tabular `/verifications` berhasil dibuat sebagai pusat kontrol operator.
- [x] **Aksi Verifikasi Cepat** — Operator dapat Approve/Reject langsung dari baris tabel tanpa masuk ke halaman detail.
- [x] **Halaman Detail Read-Only** — Panel verifikasi berhasil dihapus dari `/activities/{session}`.
- [x] **Navigasi Sidebar Terarah** — Menu "Verifikasi" mengarah langsung ke `/verifications`.

---

## 3. Tujuan Yang Belum Tercapai

Tidak ada tujuan dari `iteration.md` yang terlewat. Seluruh ruang lingkup berhasil diselesaikan.

---

## 4. Fitur Yang Ditambahkan

1. **`VerificationController`** — Controller baru yang memisahkan logika approval-flow dari `ActivityController`.
2. **Halaman `/verifications`** — View tabular antrian perjalanan `submitted` beserta aksi inline *Alpine.js*.
3. **Route baru** — `GET /verifications` dan `PATCH /verifications/{session}/verify`.
4. **Visibility Rule** — Klausa `where('status', 'verified')` aktif di 4 controller sekaligus.

---

## 5. Bug Yang Ditemukan

Tidak ada bug fungsional maupun teknis yang ditemukan selama pengujian QA.

---

## 6. Risiko Yang Masih Terbuka

1. **Tombol Aksi pada Baris `rejected`:** Baris data `rejected` di tabel `/verifications` masih menampilkan tombol Approve/Reject secara penuh. Ini berpotensi menyebabkan kebingungan operator karena data yang telah ditolak seharusnya tidak memerlukan aksi lanjutan dalam kondisi normal.

2. **Alasan Penolakan Tidak Tersimpan:** Alasan yang diisi operator saat menolak tidak direkam permanen ke database. Tidak ada jejak rekam yang bisa ditelusuri — mengurangi akuntabilitas dan nilai audit proses verifikasi.

3. **Reset Status oleh Mobile Sync:** `SyncController` selalu mengatur status ke `submitted` pada setiap sinkronisasi. Perjalanan yang telah `verified` atau `rejected` dapat kembali ke `submitted` jika petugas lapangan melakukan resync, yang berpotensi merusak integritas data yang sudah diverifikasi.

4. **Akses Tanpa Autentikasi:** Halaman `/verifications` dan semua endpoint PATCH-nya masih dapat diakses oleh siapapun yang mengetahui URL-nya.

---

## 7. Catatan Untuk Iterasi Berikutnya

Berdasarkan temuan QA dan risiko yang masih terbuka, poin-poin berikut direkomendasikan sebagai bahan pertimbangan untuk Iterasi 03 atau iterasi perbaikan minor:

1. **Sembunyikan tombol aksi untuk baris `rejected`** — Di halaman `/verifications`, baris dengan status `rejected` idealnya hanya menampilkan *badge* status tanpa tombol aksi. Ini merupakan perbaikan kecil namun berdampak besar pada kejelasan alur kerja operator.

2. **Keputusan desain: apakah `rejected` bisa di-approve kembali?** — Perlu dikomunikasikan kepada *stakeholder*: apakah perjalanan yang pernah ditolak boleh di-approve ulang (misalnya setelah data diperbaiki oleh petugas), atau apakah penolakan bersifat final? Keputusan ini akan menentukan logika tampilan dan aksi di tabel verifikasi.

3. **Pertimbangkan penyimpanan alasan penolakan** — Jika akuntabilitas dibutuhkan, iterasi mendatang dapat mempertimbangkan penambahan kolom `rejection_reason` pada tabel `tracking_sessions` (memerlukan migration).

4. **Iterasi 03 sesuai Roadmap: Verifikasi Temuan** — Roadmap menetapkan Iterasi 03 untuk proses verifikasi `ActivityEvent`. Pola dan infrastruktur dari Iterasi 02 (VerificationController, tabular view) dapat menjadi referensi langsung.

---

## 8. Status Iterasi

**Completed With Notes**
