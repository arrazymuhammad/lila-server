# Roadmap Review — Pasca Iterasi 03 (03-A & 03-B)

> Dibuat: 2026-06-21
> Konteks: Iterasi 01, 02, 03-A, 03-B telah selesai. Issue baru ditemukan dari UX Review.

---

## Ringkasan Evaluasi

Empat iterasi telah selesai dengan baik. Sistem verifikasi dua tingkat (perjalanan + temuan) kini lengkap. Namun, UX Review dari Gemini mengungkapkan **kesenjangan besar** antara visi produk (Observation-Centric) dan tampilan UI saat ini yang masih Tracking-Centric. Dashboard dan Daftar Perjalanan masih mempromosikan metrik jarak dan durasi sebagai indikator utama, bukan temuan lapangan.

Ini bukan sekadar masalah estetika — ini adalah **kontradiksi visi produk** yang harus diselesaikan sebelum LILA dapat digunakan sebagai alat analisis lapangan yang efektif.

---

## Prioritas Yang Berubah

**Sebelum review ini**, urutan roadmap adalah:
```
4. Heatmap Perjalanan
5. Kategori Temuan
6. Heatmap Temuan
7. Pelaporan dan Statistik
```

**Sesudah review ini**, ada kebutuhan baru yang mendesak:
- **Reorientasi UI Dashboard & Daftar Perjalanan** — temuan dari UX Review (ISS-001 s/d ISS-005) bersifat mendesak karena menyentuh fondasi visi produk. Jika dibiarkan, semua iterasi berikutnya (heatmap, kategori, laporan) akan dibangun di atas UI yang masih mengkomunikasikan pesan yang salah.

---

## Item Baru Yang Diusulkan

### Iterasi 4 (Revisi): Reorientasi UI — Observation-Centric

Menggantikan atau mendahului Heatmap Perjalanan, iterasi ini mengubah prioritas visual pada dua halaman utama:

**Dashboard (`/dashboard`):**
- Ganti grafik tren dari "Jarak per hari" → "Jumlah Temuan per hari"
- Ubah widget utama untuk menonjolkan Temuan dan Foto
- Evaluasi ulang widget "Perjalanan Terjauh" — pertimbangkan ganti dengan "Perjalanan Terkaya Temuan"

**Daftar Perjalanan (`/activities`):**
- Reorder summary cards: Temuan → Foto → Total Perjalanan → Total Jarak
- Ubah formula progress bar: beri bobot pengali untuk temuan dan foto
- Tambahkan toggle filter: "Hanya perjalanan dengan temuan"
- Pastikan sorting "Temuan terbanyak" tersedia dan menjadi opsi default utama

---

## Item Yang Ditunda

**Iterasi 4 lama (Heatmap Perjalanan):** Ditunda ke posisi 5 atau setelah Reorientasi UI selesai.

**Catatan:** Heatmap Perjalanan tetap relevan dan penting, namun tanpa UI yang observation-centric, heatmap yang dibangun akan tetap dilihat dalam konteks yang salah.

---

## Item Yang Berubah Cakupan

**Iterasi 5 (Kategori Temuan):** Sebagian sudah diselesaikan di Iterasi 03-B (operator_category). Yang tersisa adalah manajemen kategori master (tabel CRUD). Cakupan iterasi ini perlu direvisi menjadi lebih spesifik: **Manajemen Kategori Master**.

---

## Item Yang Dihapus

Tidak ada item yang dihapus dari roadmap.

---

## Rekomendasi Roadmap Baru

```
✅ Iteration 1:   Verifikasi Perjalanan              [SELESAI]
✅ Iteration 2:   Visibility Rule                    [SELESAI]
✅ Iteration 3-A: Verifikasi Temuan (Inti)           [SELESAI]
✅ Iteration 3-B: Pengayaan Kategori Temuan          [SELESAI]
🔄 Iteration 4:   Reorientasi UI Observation-Centric [BARU — MENDESAK]
📋 Iteration 5:   Heatmap Perjalanan                 [DIGESER dari #4]
📋 Iteration 6:   Manajemen Kategori Master          [DIREVISI dari Kategori Temuan]
📋 Iteration 7:   Heatmap Temuan                     [TETAP]
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan   [TETAP]
```

## Keputusan

- **Revisi roadmap** — Sisipkan Iterasi 4 baru (Reorientasi UI) sebelum Heatmap Perjalanan.
- **Revisi cakupan** — Iterasi Kategori Temuan diubah menjadi Manajemen Kategori Master.
- **Geser nomor** — Semua iterasi setelah #4 bergeser satu nomor.
