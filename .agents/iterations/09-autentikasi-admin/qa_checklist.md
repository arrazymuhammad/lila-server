# QA Checklist: Iterasi 09 - Autentikasi Admin & Proteksi Dashboard

## Functional Test

- [x] Mengakses halaman `/dashboard` tanpa login, secara otomatis diarahkan ke halaman `/login`.
- [x] Mengakses halaman lain seperti `/activities`, `/findings`, atau `/maps` tanpa login, juga diarahkan ke `/login`.
- [x] Memasukkan email atau password yang salah di halaman login, form akan menampilkan pesan error validasi.
- [x] Memasukkan email dan password Admin yang benar, berhasil masuk ke `/dashboard`.
- [x] Mengklik tombol "Logout" pada sidebar akan mengakhiri sesi pengguna dan mengarahkan kembali ke `/login`.
- [x] Fitur "Ingat Saya" (Remember Me) saat login berfungsi mempertahankan sesi walaupun browser ditutup dan dibuka kembali.

## UI Test

- [x] Halaman login (`/login`) memiliki tampilan yang rapi (menggunakan komponen modern *card*, responsif di HP/Desktop).
- [x] Pada bagian bawah *sidebar* di *dashboard*, terdapat informasi nama, inisial (avatar lingkaran), dan email user yang sedang login.
- [x] Tombol "Logout" pada *sidebar* terlihat mencolok, memiliki ikon *logout*, dan bereaksi dengan warna merah saat di-hover.
- [x] Di halaman Beranda/Landing Page (`/`), teks tombol tautan dashboard berubah secara cerdas:
    - Jika user belum login: **Login Operator**
    - Jika user sudah login: **Buka Dashboard**

## Regression Test

- [x] Memastikan menu navigasi lain di dalam *dashboard* (Perjalanan, Temuan, dll.) tetap berfungsi normal setelah adanya penambahan informasi user di *sidebar*.
- [x] **Kritis**: Endpoint sinkronisasi mobile (`POST /api/sync`) masih dapat di-POST dengan sukses tanpa memerlukan autentikasi login (Silakan uji via aplikasi mobile LILA atau *Postman*).

## Acceptance Criteria

- [x] Tidak ada lagi satu pun area analitik/operasional web yang bisa diintip oleh publik tanpa akun Admin.
- [x] Fitur Login dan Logout berjalan sempurna tanpa merusak arsitektur layout TailwindCSS yang sudah ada sebelumnya.
- [x] Aplikasi Mobile tetap bisa melakukan sinkronisasi data dengan mulus ke server.

## Notes

1. Pastikan Anda melakukan pengujian ini baik di perangkat Desktop maupun Mobile (minimal simulasi Inspect Element) untuk menguji responsivitas halaman Login dan Sidebar.
2. Jika ingin mengetes koneksi Mobile API, silakan kirim file ZIP *dummy* ke `/api/sync` melalui Postman tanpa header Authorization.
