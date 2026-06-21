# Infrastructure Requirement (Manual Action)

Dokumen ini memuat daftar tugas atau persiapan sistem yang **berada di luar batas kewenangan agen AI** (berdasarkan aturan di `AI_CONTEXT.md`), dan harus dijalankan secara manual oleh pengguna sebelum implementasi Iterasi 09 dapat dimulai.

## Latar Belakang
Iterasi 09 mewajibkan adanya minimal 1 akun Admin di *database* agar proses Login dapat diuji dan digunakan.
Namun, file *Seeder* berada di direktori `database/` yang bersifat **Forbidden** untuk diubah oleh AI. Begitu pula modifikasi langsung terhadap tabel *database*.

## Action Required

Silakan lakukan **salah satu** dari langkah berikut:

### Opsi 1: Menjalankan Seeder Bawaan
Saya mengecek bahwa `database/seeders/DatabaseSeeder.php` saat ini sudah menyiapkan satu pengguna uji coba:
- **Email:** `test@example.com`
- **Password:** `password` (default dari User Factory Laravel)

Jika Anda setuju menggunakan akun ini, silakan jalankan command berikut di terminal proyek Anda:
```bash
php artisan migrate:fresh --seed
```
*(Catatan: Ini akan me-reset seluruh database Anda. Lakukan hanya jika database belum berisi data penting/produksi).*

### Opsi 2: Membuat Akun Manual via Tinker
Jika Anda ingin mempertahankan data yang sudah ada di database, Anda dapat membuat 1 akun Admin secara manual melalui Tinker. Jalankan di terminal:
```bash
php artisan tinker
```
Kemudian di dalam console Tinker, jalankan perintah ini:
```php
App\Models\User::create([
    'name' => 'Admin LILA',
    'email' => 'admin@lila.com',
    'password' => bcrypt('password123'),
]);
```
- **Email:** `admin@lila.com`
- **Password:** `password123`

---

Jika Anda sudah menyelesaikan salah satu opsi di atas dan mengonfirmasi keberadaan akun Admin, **silakan beri tahu saya**, dan saya akan langsung memulai tahap *coding* implementasi (membuat `AuthController`, merombak UI, dan menyeting middleware).
