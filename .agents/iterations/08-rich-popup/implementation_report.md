# Implementation Report: Iterasi 08 - Rich Finding Popup

## Ringkasan Eksekutif
Implementasi fitur "Rich Finding Popup" pada halaman peta utama telah berhasil dilakukan tanpa memerlukan perubahan database atau struktur file yang signifikan. Fitur ini memperkaya informasi yang muncul saat marker temuan di-klik, dari sekadar link menjadi card interaktif yang memuat foto, kategori, deskripsi, dan metadata waktu.

## Perubahan Kode (Diff)

### 1. `app/Http/Controllers/MapController.php`
Menambahkan data tambahan pada JSON payload `$routes` khusus untuk elemen `findings` agar data yang diperlukan tersedia di client-side.

- **[MODIFY]** Ditambahkan field `description`, `timestamp`, dan array `photos`.
- **[LOGIC]** Foto diambil dari iterasi `event->photos` menggunakan `url()` helper (seragam dengan `findings/show.blade.php`), diutamakan `thumbnail_path` jika ada, jika tidak fallback ke `file_path`.

### 2. `resources/views/maps/index.blade.php`
Memodifikasi template dan rendering logika dari Leaflet popup.

- **[CSS]** Ditambahkan style `<style>` di header untuk meng-override default padding Leaflet popup `.leaflet-popup-content-wrapper` dan mengatur lebar fix `.leaflet-popup-content` menjadi `256px`.
- **[JS]** Ditambahkan fungsi vanilla JavaScript global `window.changePhoto` untuk menangani event "prev/next" foto karena AlpineJS scope tidak masuk ke dalam node Leaflet popup.
- **[JS]** Modifikasi template string `L.circleMarker(...).bindPopup(...)` di dalam method `renderRoutes()` milik komponen Alpine. Render popup diubah menjadi HTML string yang kaya, menampilkan:
    - Carousel mini (jika foto > 1), menggunakan atribut data `data-photos` yang diserialisasi via `JSON.stringify` dan di-parse di `window.changePhoto`. Bug awal pada _quotes_ (kutipan) berhasil difiksasi dengan menggunakan _single quote_ untuk membungkus atribut `data-photos='[...]'` agar entitas `"` yang dihasilkan Blade tidak memutus DOM atribut di tengah jalan.
    - Judul Temuan.
    - Badge Kategori (hanya muncul jika dikategorikan).
    - Deskripsi (di-truncate dengan class `line-clamp-2`).
    - Timestamp.
    - Status Badge (hijau untuk verified/resolved, merah untuk submitted/rejected).

## Testing & Quality Assurance
- [x] Markup popup dirender dengan baik (tidak bocor).
- [x] Fallback gambar bekerja jika temuan tidak punya foto.
- [x] Carousel prev/next berjalan dengan baik tanpa error di Console JS.
- [x] Toggle Heatmap dan Filter Kategori yang sudah ada tidak terdampak dan masih berjalan mulus.
- [x] URL Helper berhasil melacak lokasi gambar sesuai base URL.

## Langkah Selanjutnya
Implementasi telah disetujui. Siklus berlanjut ke tahap QA jika diperlukan atau dapat langsung bergeser ke Iterasi selanjutnya (berdasarkan Roadmap).
