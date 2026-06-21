# Technical Review: Iterasi 05
## Heatmap Perjalanan

> Dibuat: 2026-06-21  
> Status Review: **CLEAR** 🟢  

---

## 1. Evaluasi Ruang Lingkup

Apakah ruang lingkup yang dijelaskan dalam `iteration.md` sudah cukup jelas dan dapat diimplementasikan?
*   **Ya, sangat jelas.** Ruang lingkup difokuskan sepenuhnya pada modifikasi visualisasi halaman pemetaan `/map` pada sisi client-side menggunakan Leaflet dan AlpineJS.
*   Data koordinat track points (`track_points`) saat ini sudah dimuat oleh `MapController` dan diformat dengan baik ke dalam array koordinat `[latitude, longitude]`. Kita hanya perlu memproses array ini menjadi format satu dimensi yang rata (flat array of coordinates) untuk diumpankan ke generator heatmap.

---

## 2. Konflik Dengan Sistem yang Ada

Apakah ada potensi konflik dengan logika bisnis, routing, database, atau tampilan saat ini?
*   **Minimal/Hampir Tidak Ada.**
*   Fitur ini murni penambahan opsi layer di atas peta Leaflet yang sudah ada. Kita tidak mengubah struktur data, model, database schema, ataupun route controller.
*   Pemuatan data didasarkan pada data perjalanan terverifikasi (`verified`) yang sudah terfilter berdasarkan bulan dan tahun, sehingga konsisten dengan aturan *Visibility Rule* yang diterapkan sejak Iterasi 02.
*   Tidak ada modifikasi pada API controller mobile (`/api/*`), sehingga dijamin 100% kompatibel dan aman untuk aplikasi mobile.

---

## 3. Evaluasi Kebutuhan Library Eksternal

Apakah diperlukan penambahan library eksternal? Bagaimana cara integrasinya?
*   **Ya.** Diperlukan library Leaflet Heatmap plugin (`Leaflet.heat` oleh Vladimir Agafonkin).
*   **Cara Integrasi**: Karena Leaflet dimuat secara dinamis via unpkg CDN pada view `maps/index.blade.php`, plugin heatmap juga akan dimuat menggunakan script tag CDN yang sama:
    ```html
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    ```
*   Pendekatan ini sangat tepat karena tidak memerlukan instalasi npm package baru di `package.json` (yang dibatasi oleh aturan *Critical Rules* pada area *Forbidden*). Integrasi via CDN aman, ringan, dan langsung berfungsi.

---

## 4. Analisis Risiko Tambahan

Apakah ada risiko teknis atau performa yang belum tercakup di `iteration.md`?
1.  **Risiko Memori Browser (Client-side Rendering Lag)**:
    Jika jumlah perjalanan terverifikasi dalam satu bulan sangat banyak (misalnya ratusan perjalanan dengan puluhan ribu koordinat track points), rendering heatmap client-side menggunakan CPU browser bisa memicu lag pada perangkat dengan RAM/CPU rendah.
    *Mitigasi*: Untuk tahap awal, gunakan filter default per bulan yang membatasi volume data secara alami. Jika di masa mendatang volume data satu bulan melebihi ambang batas tertentu (misal > 20.000 track points), kita bisa menambahkan fungsi sampling sederhana pada controller (mengambil setiap koordinat ke-N) atau menggunakan optimalisasi Leaflet.
2.  **Sinkronisasi Toggle AlpineJS**:
    Perlu dipastikan integrasi antara state AlpineJS (`showHeatmap`) dan inisialisasi layer Leaflet disinkronkan secara benar untuk menghindari error "layer not found" atau duplikasi layer saat beralih visualisasi.
    *Mitigasi*: Buat helper method di AlpineJS (`renderRoutes()`) yang secara bersih menghapus semua layer aktif (`mapLayers`) sebelum menggambar ulang rute atau heatmap.

---

## 5. Estimasi File yang Berubah

| File Path | Peran | Tipe Perubahan |
|---|---|---|
| [index.blade.php](file:///d:/laragon/www/lila/resources/views/maps/index.blade.php) | Menyediakan elemen kontrol toggle UI, memuat library CDN `leaflet-heat.js`, dan memperbarui objek AlpineJS `allRoutesMap()` untuk render heatmap layer. | **MODIFY** |
| [MapController.php](file:///d:/laragon/www/lila/app/Http/Controllers/MapController.php) | Memeriksa apakah format data koordinat sudah optimal. Tidak ada perubahan logika bisnis, hanya memastikan optimalisasi query jika diperlukan. | **NO CHANGE** / **OPTIMIZE ONLY** |

---

## 6. Urutan Implementasi yang Disarankan

1.  **Langkah 1: Setup Library & UI Control**
    *   Tambahkan tag script CDN `leaflet-heat.js` ke [index.blade.php](file:///d:/laragon/www/lila/resources/views/maps/index.blade.php).
    *   Buat toggle UI baru "Tampilan Heatmap Perjalanan" di samping filter "Semua Temuan".
    *   Inisialisasi state AlpineJS `showHeatmap` yang di-bind ke `localStorage` (agar persisten).
2.  **Langkah 2: Integrasi Layer Heatmap**
    *   Modifikasi metode `renderRoutes()` pada script view.
    *   Jika `showHeatmap` bernilai `true`:
        *   Ekstrak semua koordinat dari `routes` menjadi single flat array: `let heatPoints = []`.
        *   Buat layer heatmap menggunakan `L.heatLayer(heatPoints, {radius: 25, blur: 15}).addTo(this.map)`.
        *   Simpan referensi layer ke array `mapLayers` untuk pembersihan saat toggle berubah.
    *   Jika `showHeatmap` bernilai `false`, render line polyline standar seperti biasa.
3.  **Langkah 3: Pengujian & QA**
    *   Uji transisi toggle berulang kali.
    *   Verifikasi bahwa filter bulan & tahun tetap berfungsi memperbarui data heatmap.
    *   Uji performa rendering pada browser.

---

## 7. Kesimpulan Status Review

Status Akhir: **CLEAR** 🟢

Semua aspek teknis telah dievaluasi dan tidak ditemukan hambatan (blocker) maupun konflik arsitektur. Iterasi 05 siap untuk diimplementasikan setelah disetujui.
