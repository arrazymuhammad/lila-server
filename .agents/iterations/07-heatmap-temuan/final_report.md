# Final Report

## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

---

## 1. Ringkasan Iterasi
Iterasi 07 berfokus pada penyajian visualisasi layer heatmap di peta utama (`/map`) yang berpusat pada sebaran data temuan, dan dilengkapi dengan alat filter (dropdown) berdasarkan kategori master. Iterasi ini merespons kebutuhan operator untuk segera mengidentifikasi titik panas (hotspot) masalah lapangan tertentu.

## 2. Fitur Yang Ditambahkan
1. **Toggle Heatmap Temuan**: Mengaktifkan layer densitas temuan. Dirancang *mutually exclusive* dengan toggle Heatmap Rute.
2. **Filter Master Kategori**: Saat mode Heatmap Temuan aktif, dropdown kategori muncul, memungkinkan filter *real-time* tanpa page reload terhadap seluruh titik panas temuan.
3. **Persistensi State**: Pilihan pengguna menyimpan preference toggle di `localStorage` peramban.

## 3. Bug / Temuan Minor
- Selama sesi QA, user meminta agar layer heatmap rute otomatis mati saat heatmap temuan dihidupkan (dan sebaliknya). Hal ini direspons langsung di dalam sprint (in-sprint fix).
- User juga meminta agar marker bulat individu tetap tergambar (namun ter-filter) kendati heatmap layer dinyalakan, agar link detail popup masih bisa di-klik. Perbaikan sudah langsung disisipkan.

## 4. Deviasi dari Rencana
- Permintaan QA mengenai *Rich Popup Preview* (popup Leaflet dengan carousel foto, timestamp, deskripsi panjang) ditunda ke iterasi terpisah. Iterasi 07 dinyatakan *Feature Complete* tanpa rich popup tersebut, karena scope-nya menuntut perubahan payload besar di dalam query Eloquent `MapController`.

## 5. Status Iterasi
**Selesai dan Ditutup (Completed).**
