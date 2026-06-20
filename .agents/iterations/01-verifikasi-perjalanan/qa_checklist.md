# QA Checklist

## Functional Test

[x] Buka menu sidebar "Verifikasi" dan pastikan ia mengarahkan ke halaman daftar perjalanan dengan filter `status=submitted` secara otomatis.
[x] Buka halaman detail perjalanan (`/activities/{session}`) untuk data yang berstatus `submitted` dan pastikan panel aksi (tombol Verifikasi dan Tolak) tampil di halaman tersebut.
[x] Buka halaman detail perjalanan untuk data yang berstatus `verified` atau `rejected` dan pastikan panel aksi TIDAK ditampilkan.
[x] Klik tombol "Verifikasi" pada perjalanan `submitted` dan pastikan data berhasil diubah statusnya menjadi `verified` tanpa ada error.
[x] Klik tombol "Tolak" pada perjalanan `submitted` dan pastikan muncul form konfirmasi untuk mengisi alasan penolakan.
[x] Cobalah untuk men-submit penolakan *tanpa* mengisi alasan, dan pastikan sistem tidak memprosesnya (alasan wajib diisi).
[x] Isi alasan penolakan lalu submit, pastikan data berhasil diubah statusnya menjadi `rejected`.

## UI Test

[x] Pastikan *badge* (label indikator) status pada halaman detail langsung ter-update (menjadi "Verified" atau "Rejected") sesaat setelah aksi verifikasi/penolakan berhasil.
[x] Pastikan form input alasan penolakan yang muncul berbasis *Alpine.js* tampil dengan rapi dan tidak merusak layout sekitarnya.
[x] Pastikan tautan menu "Verifikasi" di sidebar tampil sebagaimana layaknya menu aktif lain (bisa di-klik, bukan *disabled*).

## Regression Test

[x] Buka fitur filter status manual pada halaman Daftar Perjalanan (`/activities`) dan pastikan filter `submitted`, `verified`, dan `rejected` tetap dapat digunakan secara normal.
[x] Buka halaman Dashboard dan pastikan semua data statistik dan chart tetap memuat dengan baik tanpa ada *error query*.
[x] Buka halaman Peta Temuan dan Daftar Temuan, pastikan tidak ada halaman yang menjadi *crash* akibat perubahan ini.

## Acceptance Criteria

[x] Halaman detail perjalanan menampilkan panel verifikasi jika status adalah `submitted`.
[x] Operator dapat mengklik tombol **Verifikasi** dan status berubah menjadi `verified`.
[x] Operator dapat mengklik tombol **Tolak**, mengisi alasan, dan status berubah menjadi `rejected`.
[x] Setelah verifikasi atau penolakan, badge status pada halaman diperbarui.
[x] Panel verifikasi tidak muncul jika status sudah `verified` atau `rejected`.
[x] Item "Verifikasi" di sidebar menjadi tautan aktif yang menuju ke daftar perjalanan dengan filter `submitted`.
[x] Tidak ada perubahan pada database schema, migration, model, atau API mobile.
[x] Halaman lain (dashboard, daftar temuan, peta) tidak terpengaruh oleh perubahan ini.

## Notes
1. Pertimbangkan untuk membuat url sendiri di verifikasi, sehingga bentuknya nanti tabular, bukan terintegrasi di halaman detail perjalanan, sehingga lebih mudah jika user ingin verifikasi banyak perjalanan, dan juga tidak perlu mengubah halaman detail perjalanan
2. halamn detail perjalanan menjadi halaman read-only, sehingga tidak bisa mengedit data, namun masih bisa melihat data yang ada.
3. halaman daftar perjalanan hanya akan menampilkan status verified saja. sementara status lainnya tidak akan ditampilkan. karena hanya muncul di halaman khusus untuk verifikasi
