# QA Report: Iterasi 01 - Verifikasi Perjalanan

## Status
**PASS WITH NOTES**

## Ringkasan Pengujian
Pengujian manual telah dilakukan untuk fitur Verifikasi Perjalanan berdasarkan kriteria fungsionalitas, antarmuka pengguna (UI), dan regresi. Secara keseluruhan, fungsionalitas utama telah berjalan dengan baik dan mulus sesuai spesifikasi di `iteration.md`. Aksi verifikasi dan penolakan dapat dieksekusi dengan lancar di halaman detail perjalanan, perubahan *badge* indikator seketika termuat, filter status terintegrasi baik, dan sama sekali tidak ditemukan kendala maupun *crash* pada modul (peta/dashboard) lainnya.

## Hasil Checklist
Berdasarkan dokumen referensi `qa_checklist.md`, pengujian mencatatkan hasil **kelulusan 100%**:
- **Functional Test**: 7/7 (Seluruh pengujian fungsional lulus).
- **UI Test**: 3/3 (Seluruh elemen UI tampil sesuai ekspektasi).
- **Regression Test**: 3/3 (Sistem utama tidak terdampak efek samping).
- **Acceptance Criteria**: 8/8 (Seluruh kriteria selesai terpenuhi).

## Bug Yang Ditemukan
- Tidak ditemukan indikasi bug, baik kategori kritikal maupun minor.

## Catatan QA
Tester memberikan masukan *UX/UI Design* yang signifikan dan bernilai operasional tinggi untuk dipertimbangkan pengembangannya di masa mendatang:
1. **Pemisahan Modul Verifikasi (Halaman Tersendiri):** Pertimbangkan untuk membuat URL khusus verifikasi dengan layout berformat tabular (tabel). Hal ini jauh lebih praktis jika *user* ingin melakukan verifikasi masal (banyak perjalanan sekaligus), dan akan menghindarkan modifikasi berlebih pada halaman detail.
2. **Fokus Halaman Detail (*Read-only*):** Halaman detail perjalanan disarankan diubah sifatnya menjadi murni *read-only* agar tidak ada ruang untuk perancuan fitur; khusus untuk melihat dan mengkaji rekaman data secara utuh.
3. **Distribusi Visibilitas Status di Daftar:** Halaman daftar perjalanan (`/activities`) disarankan secara eksklusif *hanya* menampilkan yang bersatus `verified` secara spesifik, karena entitas `submitted` atau lainnya selayaknya ditangani khusus di modul verifikasi pada poin pertama.

## Risiko Yang Masih Ada
Mengacu pada arsitektur bawaan dan batasan yang disepakati, beberapa risiko perlu diantisipasi:
1. **Data Ter-reset oleh Mobile Sync:** Jalur `POST /api/sync` selalu mengganti `status = 'submitted'`. Terdapat risiko jika data yang sudah *verified/rejected* disinkron ulang dari perangkat seluler, maka akan kembali memicu antrian verifikasi.
2. **Hilangnya Histori Alasan:** Alasan "Tolak" saat ini hanya berlaku sebagai validasi di sisi UI sebelum status dikirim ke server. Karena belum ada modifikasi *database*, alasan ini tidak dapat dilacak kembali jika dipertanyakan di kemudian hari.
3. **Bebas Akses (Unauthenticated):** Siapapun yang memiliki akses URL `http://127.0.0.1:8000` masih bisa merubah status sesuka hati karena tidak adanya *session login/RBAC*.
