# QA Checklist

## Functional Test

[x] Buka halaman Daftar Perjalanan (`/activities`) dan pastikan dropdown/opsi filter "Status" sudah tidak ada lagi.
[x] Periksa data yang tampil di `/activities`, pastikan secara logis **hanya** menampilkan data dengan status `verified` (tidak ada `submitted` / `rejected`).
[x] Buka halaman Dashboard, pastikan metrik dan angka total yang ada merefleksikan hanya kumpulan data yang `verified`.
[x] Buka Peta Rute Utama (`/map`), pastikan rute yang digambar *polyline*-nya berasal khusus dari perjalanan yang `verified`.
[x] Buka Daftar Temuan (`/findings`), pastikan seluruh item *event* yang tampil di sana berasal eksklusif dari *session* yang berstatus `verified`.
[x] Klik menu "Verifikasi" di Sidebar, pastikan browser langsung diarahkan ke halaman antrian tabular baru (`/verifications`).
[x] Di halaman antrian verifikasi (`/verifications`), lakukan klik tombol "Verifikasi" (Approve) pada salah satu data berstatus `submitted`, pastikan eksekusi sukses dan mengubah baris tersebut menjadi `verified`.
[x] Di halaman antrian verifikasi (`/verifications`), lakukan klik tombol "Tolak" (Reject) pada data `submitted`, lengkapi form alasannya, dan pastikan eksekusi sukses merubah data menjadi `rejected`.

## UI Test

[x] Buka sembarang Halaman Detail Perjalanan (`/activities/{session}`) dan pastikan tampilan panel/tombol persetujuan yang dulunya ada kini telah terhapus, menjadikan halaman bersifat *Read-Only* secara utuh.
[x] Pada halaman tabel antrian Verifikasi, pastikan tabel termuat dengan responsif, tidak berantakan, dan nyaman dibaca (UI Tabular rapi).
[x] Pada tabel antrian Verifikasi, pastikan antarmuka form pengisian alasan "Tolak" yang berbasis *Alpine.js* muncul secara *inline* atau *modal* dengan lancar tanpa me-refresh paksa seisi halaman dan mengacaukan susunan baris.

## Regression Test

[x] Lakukan uji navigasi foto pada Halaman Detail Perjalanan (`/activities/{session}`), pastikan fitur *carousel* atau tampilan *thumbnail* foto tidak mengalami rusak interaktivitas *(regression)* pasca dihapusnya *script* verifikasi dari halaman tersebut.
[*UNTESTED*] *(Opsional)* Jika ada sarana testing API (misal Postman), *tembak* endpoint `POST /api/sync` dan pastikan perjalanan baru masih berhasil masuk dengan status awal `submitted`.

## Acceptance Criteria

[x] Dashboard (total, statistik, tren, highlight) hanya menghitung dan menampilkan perjalanan yang `verified`.
[x] Peta semua rute (`/map`) hanya menampilkan garis rute dari perjalanan yang `verified`.
[x] Daftar Temuan (`/findings`) hanya menampilkan temuan dari perjalanan yang `verified`.
[x] Daftar Perjalanan Utama (`/activities`) tidak lagi memiliki filter status, dan hanya menampilkan perjalanan `verified`.
[x] Terdapat halaman Verifikasi tabular baru yang khusus menampilkan daftar antrian.
[x] Operator dapat mengeksekusi Verifikasi dan Penolakan langsung dari baris tabel di halaman Verifikasi tersebut tanpa harus masuk ke Halaman Detail.
[x] Halaman Detail Perjalanan telah dibersihkan dari panel verifikasi (*read-only mode*).
[x] Menu "Verifikasi" di sidebar mengarah lurus ke halaman antrian tabular tersebut.

## Notes
1. Masih muncul tombol aksi lengkap di perjalanan yang telah di reject. pertimbangan apa untuk memunculkan ini. apakah tidak lebih baik di sembunyikan saja? karena statusnya kan sudah reject dan tidak akan di verifikasi lagi.
2. Jikalaupun perjalanan rejected ditampilkan kembali harus ada informasi alasan penolakan kenapa bisa menjadi rejected, saat ini tidak ada informasi mengenai alasan penolakan tersebut, sehingga user tidak tahu mengapa perjalanannya ditolak.
3. Perjalanan rejected masih bisa di approve jika user mobile melakukan sinkronisasi lagi. sehingga statusnya berubah jadi updated atau sejenisnya. pertimbangan untuk tetap seperti ini apa? tidak lebih baik jika perjalanan rejected tidak bisa di approve lagi?