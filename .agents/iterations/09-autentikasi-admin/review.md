# PRE-IMPLEMENTATION TECHNICAL REVIEW: Iterasi 09

**1. Apakah ruang lingkup sudah jelas?**
Ya, ruang lingkup sangat jelas. Iterasi ini berfokus pada penyediaan halaman login untuk Admin/Operator, pengamanan rute dashboard (seperti `/activities`, `/findings`, `/maps`) menggunakan *middleware* `auth`, penambahan fitur *logout*, dan pembuatan/pemastian satu akun admin via *seeder*. Semuanya dilakukan secara manual (tanpa Breeze/Jetstream) untuk menjaga struktur layout UI yang sudah ada.

**2. Apakah ada konflik dengan sistem saat ini?**
Ada **satu konflik kritis**:
- Dokumen `iteration.md` meminta untuk memastikan modifikasi pada `DatabaseSeeder.php` agar menghasilkan minimal 1 akun Admin. Namun, dokumen `AI_CONTEXT.md` menetapkan seluruh direktori `database/*` sebagai area **Forbidden** (Dilarang). Mengubah seeder akan melanggar aturan utama proyek ini.

**3. Apakah ada risiko yang belum disebutkan?**
- **Risiko API Mobile**: Rute `/api/sync` saat ini masih publik. Kita harus memastikan *middleware* `auth` **hanya** diterapkan di `routes/web.php` dan tidak menyentuh `routes/api.php`, agar proses sinkronisasi dari aplikasi mobile tidak rusak sebelum Iterasi 10 dikerjakan.
- **Risiko Flow Landing Page**: Halaman awal (`welcome.blade.php`) kemungkinan memiliki tautan langsung ke dashboard. Saat rute diproteksi, klik pada tautan tersebut akan me-redirect ke halaman login. Teks tombol mungkin perlu disesuaikan agar alurnya lebih natural (misal: "Login ke Dashboard").
- **Ketiadaan Reset Password**: Ruang lingkup tidak menyebutkan fitur "Lupa Password", yang berarti jika Admin lupa password, harus di-reset manual dari database.

**4. Estimasi jumlah file yang berubah:**
Sekitar **5-6 file** akan diubah atau dibuat baru.

**5. Area kode yang kemungkinan terdampak:**
- `routes/web.php` (Penambahan rute login/logout dan penerapan *route group middleware auth*).
- `app/Http/Controllers/AuthController.php` (File controller baru).
- `resources/views/auth/login.blade.php` (File view baru).
- `resources/views/layouts/app.blade.php` (Pembaruan UI sidebar/header untuk menambahkan tombol *Logout* dan nama pengguna).
- `resources/views/welcome.blade.php` (Penyesuaian link navigasi di landing page).
