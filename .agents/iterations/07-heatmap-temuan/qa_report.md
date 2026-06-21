# QA Report

## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

| Status | 🟢 Passed with Fixes |
|---|---|
| **Tanggal Pengujian** | Hari ini |
| **Lingkungan** | Local (`lila.test`) |

---

## Hasil Checklist

* **Functional Test**: ✅ 100% Passed. Heatmap Temuan berfungsi, filter kategori berjalan real-time.
* **UI Test**: ✅ 100% Passed. Warna toggle dan layer distinct.
* **Regression Test**: ✅ 100% Passed. Fitur eksisting tidak terganggu.

---

## Temuan & Resolusi (Saat Sesi QA)

1. **[Bug/UX]** Heatmap Temuan dan Heatmap Rute sebaiknya *mutually exclusive* agar peta tidak terlalu ramai.
   * **Fix**: Diubah logic toggle AlpineJS agar menyalakan satu mematikan yang lain.
2. **[Bug/UX]** Polylines rute harus disembunyikan dan map harus auto-fit ke titik temuan saat Heatmap Temuan aktif.
   * **Fix**: Logika `renderRoutes` di-update untuk tidak menggambar `L.polyline` jika heatmap temuan aktif.
3. **[Request User]** Marker temuan harus tetap terlihat saat Heatmap Temuan aktif agar popup masih bisa diklik.
   * **Fix**: Dihapus `return` awal pada looping finding. Marker tetap digambar, namun difilter sesuai opsi dropdown `selectedCategory`.
4. **[Request Fitur]** Rich Popup Preview (Carousel foto, deskripsi, timestamp) untuk marker temuan.
   * **Resolusi**: Scope terlalu besar untuk di-patch di iterasi ini karena membutuhkan perubahan payload mapping data di `MapController`. Dicatat sebagai item baru (`BL-012`) dan dimasukkan ke `ROADMAP.md` sebagai **Iterasi 08**.

---

## Kesimpulan

Semua perbaikan *in-sprint* sudah dikerjakan dan diverifikasi berjalan dengan baik. Fitur besar yang diusulkan sudah dipetakan dengan rapi ke roadmap. Iterasi 07 selesai.
