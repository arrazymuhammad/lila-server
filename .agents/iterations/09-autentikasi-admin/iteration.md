# Iterasi 09 — Autentikasi Admin & Proteksi Dashboard

> Status: **Draft**
> Target Mulai: Segera
> Fitur: Login Web App dan Middleware Auth

---

## 1. Latar Belakang

Berdasarkan `SYSTEM_ANALYSIS.md`, salah satu kelemahan kritis dari aplikasi web LILA saat ini adalah *tidak adanya autentikasi (Zero Authentication)*. Seluruh halaman web (Dashboard, Peta, Detail Perjalanan, Detail Temuan) bersifat publik dan dapat diakses oleh siapa saja. Hal ini sangat rentan bagi aplikasi pencatatan operasional lapangan yang mengandung data geolokasi spesifik.

Sesuai arahan ROADMAP terbaru, kita beralih prioritas untuk langsung menangani masalah keamanan ini pada Iterasi 09.

---

## 2. Masalah yang Diselesaikan

- Akses publik tanpa batas ke dalam dashboard operasional.
- Ketiadaan kontrol hak akses terhadap data temuan, foto, dan tracking perjalanan.

---

## 3. Ruang Lingkup

1. **Sistem Login:**
   - Membuat halaman Login untuk Operator/Admin. Karena LILA Web diperuntukkan hanya bagi operator internal, halaman Register tidak akan disediakan di ranah publik web ini.
   - Menyediakan fitur *Logout*.

2. **Middleware Auth:**
   - Mengelompokkan seluruh *route* dashboard (termasuk `/activities`, `/findings`, `/maps`, `/verifications`, dsb.) ke dalam grup *route* yang diamankan oleh middleware `auth`.
   - Modifikasi `routes/web.php` untuk memisahkan *public routes* (contoh: `/` dan login) dari *protected routes*.

3. **Seeder Akun Pertama:**
   - Memastikan `DatabaseSeeder.php` atau seeder spesifik menghasilkan minimal 1 akun Admin aktif yang kredensialnya (email/password) dapat digunakan untuk login pasca instalasi.

4. **Kecocokan Layout:**
   - Menggunakan TailwindCSS v4 pada view login agar seragam dengan desain `layouts/app.blade.php`.

---

## 4. Pra-syarat & Perhatian

- **Tidak Disarankan Menggunakan Starter Kit Penuh (Breeze/Jetstream) Secara Paksa:**
  LILA sudah memiliki *layout* custom (`app.blade.php`). Jika kita menginstall Breeze via artisan `breeze:install`, ada risiko struktur view dan CSS Tailwind bawaan proyek tertimpa. Langkah paling aman adalah membuat `LoginController` manual atau hanya mengambil *stub views/controllers* spesifik auth tanpa merusak arsitektur UI yang ada.
- Jangan lupa menambahkan tombol "Logout" di suatu tempat pada Navbar atau Sidebar di `layouts/app.blade.php`.
