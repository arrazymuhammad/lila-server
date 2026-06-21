# QA Checklist: Iterasi 08 - Rich Finding Popup

## 1. Peta Utama (/map)
- [ ] **Data Payload findings**
  - [ ] Memastikan `description`, `timestamp`, dan `photos` terisi di payload JSON (`$routes`) di [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php).
  - [ ] Memastikan field URL foto menggunakan fungsi `url()` Laravel.
- [ ] **Render Marker & Popup**
  - [ ] Memastikan popup Leaflet memiliki max-width 256px dan ter-style rapi tanpa merusak layout luar.
  - [ ] Memastikan foto utama tampil jika data `photos` tersedia.
  - [ ] Memastikan fallback text "Tidak ada foto" tampil jika data `photos` kosong.
  - [ ] Memastikan judul temuan, kategori (badge), deskripsi (truncated), timestamp, dan status tampil sesuai spesifikasi.
  - [ ] Memastikan tombol detail ("Detail ->") mengarah ke target URL detail temuan yang valid (misal `/findings/{id}`) untuk status selain `submitted`.
- [ ] **Logika Carousel Foto**
  - [ ] Memastikan tombol prev (`&lsaquo;`) dan next (`&rsaquo;`) hanya tampil jika `photos` > 1.
  - [ ] Memastikan interaksi tombol prev/next mengubah foto yang aktif secara siklik (circular).
  - [ ] Memastikan index text (e.g. `1/3`) terupdate secara dinamis di pojok kanan atas foto.
  - [ ] Memastikan tidak ada runtime error JS saat menekan tombol navigasi foto.

## 2. Regression Testing
- [ ] **Heatmap & Filter**
  - [ ] Memastikan toggle "Heatmap Rute" tetap merender heatmap koordinat perjalanan secara normal.
  - [ ] Memastikan toggle "Heatmap Temuan" tetap merender heatmap temuan secara normal.
  - [ ] Memastikan filter "Kategori Temuan" menyaring data heatmap temuan dengan benar.
  - [ ] Memastikan toggle "Semua Temuan" tetap menyaring status temuan (verified vs all) tanpa error.
- [ ] **Kinerja Halaman**
  - [ ] Payload JSON tidak menyebabkan crash browser ketika volume koordinat data besar.
