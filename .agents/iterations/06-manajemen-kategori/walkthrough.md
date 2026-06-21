# Walkthrough: Manajemen Kategori Master
## Iterasi 06

Implementasi Iterasi 06 ini menyelesaikan tantangan data tak terstruktur dengan menambahkan kontrol pada penamaan Kategori Temuan Lapangan. 

Berikut fitur-fitur yang sudah terpasang:

### 1. Halaman Manajemen Kategori

- Pergi ke `lila.test/categories`.
- Halaman ini memungkinkan Anda menambah Master Kategori baru yang baku (seperti "Alat Tangkap Terlarang", "Intrusi Jaring Trawl", dsb.).
- Kategori yang didaftarkan di sini akan mencegah duplikasi typo karena model ini akan diandalkan sebagai daftar *Autocomplete* atau *Auto-suggest*.
- Anda juga dapat menghapus kategori yang dirasa tidak perlu lagi. Tindakan menghapus kategori di sini **TIDAK AKAN MENGHAPUS** temuan-temuan lampau yang sudah kadung diberi kategori tersebut.

### 2. Auto-Suggest Saat Review Verifikasi

- Navigasi ke verifikasi (`/verifications`) dan buka sebuah proses verifikasi temuan.
- Coba ketik sesuatu pada kolom **Kategori Baku (Operator)**.
- Sekarang, list dropdown tersebut murni mengambil dari daftar Kategori yang dibuat di halaman `/categories` (tidak lagi mengambil sembarang teks unik dari data lampau yang berpotensi *typo*).

### 3. Filter Baru di Daftar Temuan

- Kunjungi `lila.test/findings`.
- Sekarang pada barisan filter pencarian, terdapat satu buah dropdown baru bernama **Kategori**.
- Operator kini dapat mengelompokkan tampilan semisal hanya mencari temuan-temuan terkait kasus *Ilegal Logging* (bila dimasukkan sebagai kategori) pada satu layar.
