# Technical Review — Iterasi 08 (Rich Finding Popup)

> Status: **APPROVED**
> Reviewer: LILA Architect

---

## 1. Evaluasi Ruang Lingkup

Ruang lingkup terdefinisi dengan sangat jelas di [`.agents/iterations/08-rich-popup/iteration.md`](.agents/iterations/08-rich-popup/iteration.md):
- Menampilkan card preview kaya di popup Leaflet pada peta utama ([`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php)).
- Menyertakan foto (carousel jika > 1), judul, kategori, deskripsi singkat, dan timestamp.
- Menyediakan link detail temuan.

---

## 2. Konflik Dengan Sistem Saat Ini

1. **Reaktivitas UI (AlpineJS vs Leaflet DOM):**
   Leaflet memotong siklus hidup AlpineJS ketika melakukan bind HTML string ke popup. Tombol carousel prev/next tidak bisa menggunakan direktif `x-on:click` AlpineJS.
   *Solusi:* Gunakan plain JavaScript event delegation melalui event `popupopen` di Leaflet `map.on('popupopen', ...)` untuk binding event klik secara dinamis.

2. **Resolusi URL Foto:**
   Berdasarkan investigasi pada [`resources/views/findings/show.blade.php`](resources/views/findings/show.blade.php:75), foto diakses via helper `url($photo->thumbnail_path ?: $photo->file_path)`.
   *Solusi:* Kontroler [`MapController.php`](app/Http/Controllers/MapController.php) harus menyusun URL absolut menggunakan helper `url()` sebelum dikirim sebagai JSON payload.

---

## 3. Evaluasi Kebutuhan Library Eksternal

- **Tidak ada library baru.** Carousel mini akan diimplementasikan menggunakan CSS dasar dan plain JavaScript minimal di dalam callback `popupopen`.

---

## 4. Analisis Risiko Tambahan

1. **Payload Size:** Jika temuan memiliki banyak foto, ukuran JSON bertambah.
   *Mitigasi:* Hanya ambil foto yang bertanda `selected = true` (atau limit maksimal 3 foto per temuan).
2. **Layout Overflow:** Deskripsi panjang merusak layout popup.
   *Mitigasi:* Gunakan pemotongan teks (truncate/clamp) di level JS/CSS.

---

## 5. Estimasi File yang Berubah

1. [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php) — Eager load dan mapping properti foto, deskripsi, & timestamp ke payload JSON.
2. [`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php) — Peningkatan script JS rendering popup Leaflet dan handler carousel event.

---

## 6. Urutan Implementasi yang Disarankan

1. Perbarui payload temuan di [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php) untuk mengirim `description`, `timestamp`, dan array `photos` (URL).
2. Buat struktur HTML popup baru di fungsi penanda peta pada [`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php).
3. Tambahkan styling CSS ringkas untuk layout card popup dan carousel.
4. Terapkan plain JS handler untuk navigasi carousel foto di dalam event listener `map.on('popupopen')`.

---

## 7. Kesimpulan Status Review

**CLEAR TO IMPLEMENT**
Ditargetkan langsung pada mode Code di langkah berikutnya.
