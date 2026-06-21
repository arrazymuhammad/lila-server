# Sprint Review: Iterasi 09

**Fokus**: Autentikasi Admin & Proteksi Dashboard  
**Status**: Selesai ✅

## Yang Berjalan Baik
1. **Pendekatan Custom**: Fitur *login* dan perlindungan sesi (*middleware*) berhasil diintegrasikan tanpa menggunakan *starter kit* penuh (seperti Breeze). Hal ini menghindarkan kita dari risiko rusaknya tata letak (layout) UI Tailwind v4 yang sudah dibangun khusus untuk LILA.
2. **Kolaborasi Hybrid**: Koordinasi antara Agen (yang tidak memiliki akses untuk mengubah direktori `database/*`) dan pengguna (yang mengeksekusi modifikasi data di *database*) berjalan lancar. Akun admin perdana sukses dibuat tanpa melanggar `AI_CONTEXT.md`.
3. **Ketahanan API**: Fokus pengamanan secara spesifik ditujukan pada *web dashboard*, membiarkan rute API (*mobile*) `/api/sync` tetap asinkron dan publik untuk sementara, mencegah potensi putusnya komunikasi dengan perangkat di lapangan secara tiba-tiba.

## Kendala & Solusi
1. **Gagal Muat CSS**: Halaman `login.blade.php` sempat kekurangan gaya (*unstyled*) karena tidak memuat *stylesheet* dengan benar jika di lingkungan tanpa *Vite dev server*. **Solusinya**, tautan CDN Tailwind disisipkan seperti yang ada pada *layout* utama, memulihkan tampilannya secara instan.
2. **Kendala Terminal Lingkungan**: Sempat terjadi masalah saat eksekusi *Git* dan kegagalan perintah `php artisan` karena inkonsistensi pembacaan variabel *environment* bawaan di dalam lingkungan agen *shell*. **Solusinya**, pengguna turun tangan mengeksekusi beberapa skrip (*commit* & *migrate*) di lingkungan terminal Laragon mandiri.

## Backlog Maintenance
Pada ulasan penutup *sprint* ini, satu target utang infrastruktur telah ditangani penuh:
- **BL-003 (Authentication & Access Control)** yang sebelumnya bertengger pada kelompok prioritas menengah (*Medium Priority*) secara resmi dinyatakan terselesaikan dan telah dipindahkan ke ruang **DONE** di dokumen `BACKLOG.md`.

---
Iterasi ini mengemas nilai bisnis penting: data lapangan LILA sekarang memiliki dinding pelindung yang solid dari intaian publik. LILA siap melangkah ke Iterasi 10!
