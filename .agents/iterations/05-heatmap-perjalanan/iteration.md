# Iterasi 05 — Heatmap Perjalanan

> Status: **Proposed**  
> Target Mulai: Pasca Persetujuan Pipeline  
> Fitur: Visualisasi Heatmap Perjalanan Lapangan  

---

## 1. Pertimbangan Backlog vs Roadmap

Berdasarkan evaluasi di `roadmap_review_003.md` dan `backlog_review.md`, tidak ada item backlog prioritas tinggi yang dapat didahulukan saat ini karena ketergantungan infrastruktur atau regulasi yang belum disetujui (seperti perubahan database schema pada BL-001/BL-009 dan koordinasi API mobile pada BL-002). 

Oleh karena itu, **Roadmap Iterasi 05: Heatmap Perjalanan** adalah prioritas terbaik untuk dieksekusi guna memberikan nilai tambah visual dan analitis langsung bagi operator monitoring dalam melacak densitas patroli/perjalanan di lapangan.

---

## 2. Latar Belakang

Berdasarkan audit kode pada:
*   [MapController.php](file:///d:/laragon/www/lila/app/Http/Controllers/MapController.php)
*   [index.blade.php](file:///d:/laragon/www/lila/resources/views/maps/index.blade.php)
*   [web.php](file:///d:/laragon/www/lila/routes/web.php)

Kondisi peta saat ini hanya menampilkan:
1.  Garis rute (`L.polyline`) perjalanan individu berdasarkan data koordinat track points terverifikasi.
2.  Marker bulat (`L.circleMarker`) untuk temuan lapangan (events).
3.  Kontrol untuk melakukan toggle "Semua Temuan".

Sistem pemetaan saat ini memuat Leaflet via CDN tanpa ada library heatmap terinstal (baik di `package.json` maupun `resources/js`). Ketika volume data perjalanan meningkat, tumpang tindih garis rute pada area yang sama membuat peta sulit dibaca dan tidak mampu merepresentasikan wilayah mana yang memiliki intensitas patroli paling tinggi secara intuitif.

---

## 3. Masalah yang Diselesaikan

1.  **Overcrowding Visual (Visual Noise)**: Penumpukan puluhan rute perjalanan (polyline) dengan warna berbeda di satu wilayah geografis membuat peta sulit diinterpretasikan.
2.  **Identifikasi Area Kurang Patroli (Patrol Gaps)**: Operator tidak memiliki cara cepat untuk menganalisis area mana yang paling sering dikunjungi (hotspots) dan area mana yang jarang/tidak pernah tersentuh patroli (coldspots).

---

## 4. Tujuan Iterasi

1.  Mengintegrasikan library heatmap Leaflet (`leaflet-heat.js`) secara aman.
2.  Menyediakan opsi visualisasi baru berupa **Heatmap Perjalanan** di halaman Peta Utama (`/map`).
3.  Menyediakan kontrol toggle UI yang intuitif untuk beralih antara tampilan Rute Standar (Polyline) dan tampilan Heatmap, atau menggabungkan keduanya.

---

## 5. Ruang Lingkup

Perubahan akan difokuskan secara eksklusif pada halaman pemetaan utama tanpa menyentuh database maupun API mobile:

### Frontend (Blade & AlpineJS)
*   **[MODIFY]** [index.blade.php](file:///d:/laragon/www/lila/resources/views/maps/index.blade.php)
    *   Tambahkan CDN library heatmap Leaflet: `<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>` di section `@section('head')`.
    *   Perbarui object Alpine.js `allRoutesMap()` untuk mengelola inisialisasi, rendering, dan toggle layer heatmap (`L.heatLayer`).
    *   Modifikasi layout header/kontrol peta untuk menambahkan switch/toggle "Tampilan Heatmap Perjalanan".
    *   Kelola penyimpanan pilihan visualisasi pengguna (rute vs heatmap) menggunakan `localStorage` agar persisten saat berpindah halaman.

### Backend (Laravel Controller & Query)
*   **[MODIFY]** [MapController.php](file:///d:/laragon/www/lila/app/Http/Controllers/MapController.php)
    *   Pastikan data koordinat track points dikirim dalam format yang efisien dan optimal untuk dikonsumsi oleh generator heatmap. (Saat ini, format koordinat sudah dikirim sebagai array `[latitude, longitude]` melalui `$session->trackPoints`, yang sudah kompatibel dengan format input `leaflet-heat.js`). Tidak ada modifikasi query data besar yang diperlukan, menjaga backend tetap ringan.

### Routing
*   **[NO CHANGE]** [web.php](file:///d:/laragon/www/lila/routes/web.php)
    *   Menggunakan route `/map` dan `/maps` yang sudah ada secara langsung tanpa mengubah URL endpoint.

---

## 6. Yang Tidak Termasuk (Out of Scope)

1.  **Heatmap Temuan (Finding Heatmap)**: Visualisasi heatmap sebaran titik temuan (events) tidak termasuk dalam iterasi ini dan akan dikerjakan pada **Iterasi 7** sesuai roadmap.
2.  **Kustomisasi Parameter Gradasi Heatmap Kompleks**: Pengaturan radius heatmap atau gradasi warna secara kustom oleh user melalui UI tidak disediakan (menggunakan parameter default yang dioptimalkan oleh sistem).
3.  **Halaman Detail Perjalanan Individual**: Detail perjalanan (`/activities/{session}`) tetap menampilkan polyline spesifik dan tidak menggunakan heatmap.
4.  **Database Migration**: Tidak ada pembuatan tabel baru, penambahan kolom, atau perubahan tipe data.

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Peta di `/map` berhasil memuat library `leaflet-heat.js` tanpa error di console.
- [ ] Tombol/toggle switch "Tampilan Heatmap" tersedia di kontrol peta.
- [ ] Ketika toggle Heatmap diaktifkan:
    *   Layer polyline rute disembunyikan (atau diatur opacity-nya menjadi sangat rendah/transparan jika digabungkan).
    *   Layer heatmap perjalanan dirender berdasarkan akumulasi koordinat seluruh track points dari session terverifikasi pada periode terpilih.
    *   Marker temuan (events) tetap dapat ditampilkan/disembunyikan sesuai toggle "Semua Temuan".
- [ ] Ketika toggle Heatmap dimatikan:
    *   Layer heatmap dihapus/disembunyikan.
    *   Layer polyline rute standar kembali ditampilkan dengan warna aslinya.
- [ ] State pilihan toggle tersimpan di `localStorage` dan dimuat ulang dengan benar saat refresh halaman.
- [ ] Tidak ada regresi pada pemuatan data bulanan/tahunan (fitur filter bulan/tahun tetap bekerja secara normal).
- [ ] Tidak ada pesan error baru pada log Laravel maupun console developer browser.

---

## 8. Risiko dan Hal yang Perlu Diperhatikan

| # | Risiko | Mitigasi |
|---|---|---|
| 1 | **Beban Kinerja Browser (Client-side Rendering)**: Memuat ribuan track points sekaligus untuk heatmap dapat memicu lag pada browser dengan spesifikasi rendah. | Batasi atau optimalisasi data track points yang dikirim jika terjadi overload. `leaflet-heat` sangat efisien untuk puluhan ribu titik, namun jika data membesar di masa mendatang, pertimbangkan sampling koordinat. |
| 2 | **Ketergantungan CDN Eksternal**: Memuat `leaflet-heat.js` dari CDN unpkg berisiko jika server CDN down atau koneksi internet offline. | Pastikan fallback loading yang aman atau gunakan asset lokal jika diperlukan di masa depan. Untuk iterasi ini, penggunaan unpkg CDN disetujui karena konsisten dengan library Leaflet utama yang sudah ada. |
| 3 | **Kompatibilitas Mobile API**: Ada kekhawatiran perubahan merusak API. | Iterasi ini 100% frontend webGIS dan tidak menyentuh endpoint API `/api/*` yang dikonsumsi oleh aplikasi mobile. Stabilitas terjamin. |

---

## 9. Dampak Terhadap Pengguna

*   **Operator Monitoring**: Mendapatkan representasi visual instan tentang densitas cakupan wilayah patroli lapangan. Area yang memiliki warna merah menyala menunjukkan tingkat frekuensi lintasan patroli yang tinggi, sedangkan area kosong menunjukkan wilayah yang terabaikan. Ini sangat membantu dalam perencanaan rute patroli berikutnya secara taktis.
