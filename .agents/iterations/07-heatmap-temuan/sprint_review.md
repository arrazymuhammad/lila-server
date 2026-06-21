# Sprint Review
## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

> Status: **Completed ✅**

---

## Ringkasan
Iterasi 07 menyelesaikan fitur utama visualisasi GIS untuk temuan lapangan. Ini adalah ekstensi logis dari Iterasi 05 (Heatmap Rute) dan Iterasi 06 (Master Kategori). Sekarang sistem dapat mengelompokkan awan kepadatan masalah lapangan secara kategori.

---

## Tujuan Yang Tercapai
- [x] Layer Heatmap untuk Temuan dengan warna gradien terpisah.
- [x] Dropdown filter kategori yang muncul dinamis berdasar master tabel `finding_categories`.
- [x] Perilaku *mutual exclusion* antara Heatmap Rute dan Heatmap Temuan (QA request).
- [x] Peta auto-fit secara spesifik ke sebaran titik temuan ketika Heatmap Temuan dihidupkan (QA request).

---

## Pembelajaran
1. **Frontend-heavy iteration sangat bergantung pada integrasi:** Manipulasi UI yang rumit (mutual exclusion toggle, menyembunyikan polyline tapi menggambar heatmap temuan + marker) lebih mudah ditangani dengan state AlpineJS (`showFindingHeatmap`, `showHeatmap`, dll) ketimbang logika imperatif Vanilla JS biasa.
2. **Feature Creep saat QA itu wajar:** Permintaan *Rich Popup* adalah konsekuensi logis dari mempermudah pandangan pengguna terhadap peta. Pengguna menyadari nilai data spasial, namun terhambat interaksi click-to-new-tab. Menunda ini ke Iterasi 08 adalah langkah arsitektural yang tepat untuk mempertahankan sprint velocity.

---

## Evaluasi Roadmap

```
✅ Iteration 1:   Verifikasi Perjalanan
✅ Iteration 2:   Visibility Rule
✅ Iteration 3-A: Verifikasi Temuan (Inti)
✅ Iteration 3-B: Pengayaan Kategori Temuan
✅ Iteration 4:   Reorientasi UI Observation-Centric
✅ Iteration 5:   Heatmap Perjalanan
✅ Iteration 6:   Manajemen Kategori Master
✅ Iteration 7:   Heatmap Temuan Berdasarkan Kategori
📋 Iteration 8:   Peta Interaktif: Rich Finding Popup  [BERIKUTNYA]
📋 Iteration 9:   Pelaporan dan Statistik Lanjutan     [DIRENCANAKAN]
```

---

## Keputusan
- Tutup Sprint Iterasi 07.
- Siapkan Iterasi 08 (Peta Interaktif: Rich Finding Popup).
