# Implementation Plan: Iterasi 09 - Autentikasi Admin & Proteksi Dashboard

## Goal Description

Menyediakan sistem login khusus bagi Operator/Admin untuk memproteksi seluruh halaman *dashboard* operasional (`/activities`, `/findings`, `/maps`, dll). Sistem ini akan dibangun secara manual tanpa memaksakan *starter kit* (Breeze/Jetstream) guna menjaga konsistensi arsitektur dan layout UI TailwindCSS v4 yang sudah ada.

## Proposed Changes

### Http Controllers & Routes

Akan ditambahkan controller baru untuk menangani logika otentikasi.

#### [NEW] app/Http/Controllers/AuthController.php
- Membuat method `showLoginForm()` untuk menampilkan view login.
- Membuat method `login()` untuk memvalidasi *credentials* (`email`, `password`) dan melakukan proses otentikasi menggunakan `Auth::attempt`.
- Membuat method `logout()` untuk mengeluarkan pengguna dan menghapus *session*.

#### [MODIFY] routes/web.php
- Menambahkan route `GET /login`, `POST /login`, `POST /logout`.
- Membungkus route `/dashboard`, `/activities`, `/activities/{session}`, `/findings`, `/findings/{event}`, `/map`, dan `/maps` di dalam *route group* dengan `middleware('auth')`.

### Views & UI

Akan ditambahkan halaman login dan tombol *logout* pada layout.

#### [NEW] resources/views/auth/login.blade.php
- Menggunakan desain premium yang sesuai dengan pedoman estetika. Form login akan menggunakan TailwindCSS v4.
- Akan menggunakan *card* modern dengan pesan error jika login gagal.
- Menggunakan *layout* dasar yang tidak bentrok dengan `layouts/app.blade.php`.

#### [MODIFY] resources/views/layouts/app.blade.php
- Menambahkan informasi Admin yang sedang login (misal: nama user) di *sidebar* atau *header*.
- Menambahkan tombol atau link **Logout** (berupa form POST) pada sidebar.

#### [MODIFY] resources/views/welcome.blade.php
- Menyesuaikan tombol "Buka Dashboard" agar sinkron dengan *flow* baru (otomatis diarahkan ke halaman login oleh middleware).

## Verification Plan

### Manual Verification
1. **Akses URL Langsung**: Mencoba mengakses `/dashboard` tanpa login. Harus dialihkan ke `/login`.
2. **Proses Login Gagal**: Memasukkan email/password yang salah. Harus mengembalikan error.
3. **Proses Login Berhasil**: Memasukkan email/password yang benar. Harus masuk ke `/dashboard`.
4. **Proses Logout**: Mengklik "Logout". Sesi harus berakhir.
5. **Mobile API (Regresi)**: Memastikan `POST /api/sync` masih bisa diakses publik (tanpa token).
