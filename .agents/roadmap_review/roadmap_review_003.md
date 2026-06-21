# Roadmap Review — Pasca Iterasi 04 (Reorientasi UI Observation-Centric)

> Dibuat: 2026-06-21
> Konteks: Iterasi 04 (Reorientasi UI Observation-Centric) telah selesai sepenuhnya dengan status "Clean ✅". Persiapan untuk Iterasi 05.

---

## Ringkasan Evaluasi

Iterasi 04 telah diselesaikan dengan sangat bersih tanpa adanya bug, regresi, atau deviasi signifikan. Semua issue (ISS-001 s/d ISS-005) telah diselesaikan, sehingga antarmuka utama (Dashboard dan Daftar Perjalanan) kini telah sepenuhnya selaras dengan visi *Observation-Centric*.

Dengan selesainya Iterasi 04, landasan visual dan logika metrik LILA sudah kokoh. Langkah logis berikutnya adalah meningkatkan visualisasi spasial perjalanan melalui fitur **Heatmap Perjalanan** (Iterasi 5).

---

## Evaluasi Pertanyaan Utama

### 1. Apakah urutan iterasi berikutnya masih tepat?
**Ya.** Urutan berikutnya sesuai roadmap aktif, yaitu **Iteration 5: Heatmap Perjalanan**, adalah pilihan yang paling tepat. Modul peta (`/map`) saat ini baru menampilkan marker temuan terverifikasi dan garis perjalanan standar. Visualisasi sebaran perjalanan melalui heatmap akan memberikan insight spasial yang signifikan bagi operator monitoring untuk melihat area mana saja yang sering dipatroli atau dilewati.

### 2. Apakah ada item backlog yang lebih mendesak dari Iterasi 5?
**Tidak ada.** Meskipun terdapat beberapa item backlog dengan prioritas tinggi, semuanya memiliki kendala/ketergantungan yang belum terpecahkan:
*   **BL-001 (Rejected Reason Persistence - High)**: Membutuhkan perubahan skema database (kolom baru untuk menyimpan alasan penolakan). Sesuai aturan *Critical Rules*, migrasi database dilarang tanpa persetujuan eksplisit.
*   **BL-002 (SyncController Status Reset - High)**: Membutuhkan koordinasi dan potensi perubahan API kontrak dengan aplikasi mobile, yang merupakan prioritas untuk dihindari demi menjaga kompatibilitas mobile.
*   **BL-009 (Manajemen Kategori Master - High)**: Ketergantungan terhadap skema database baru (tabel `finding_categories`). Fitur ini sudah terjadwal setelah Heatmap Perjalanan (sebagai Iterasi 6) agar keputusan skema dapat dikonsolidasikan dengan baik.
*   **BL-007 (Edit Mode untuk Temuan Verified - Medium)**: Merupakan peningkatan kualitas UX minor dan tidak memiliki tingkat urgensi yang melebihi visualisasi spasial perjalanan.

### 3. Apakah ada risiko aktif yang mengubah prioritas?
**Tidak ada.** Semua issue terdaftar (ISS-001 s/d ISS-005) telah berstatus *Resolved*. Tidak ada laporan bug kritis, masalah keamanan, atau regresi performa dari Iterasi 04 yang membutuhkan penanganan darurat.

---

## Prioritas Yang Berubah

Tidak ada perubahan prioritas pada roadmap aktif pasca Iterasi 04. Iterasi 5 (Heatmap Perjalanan) tetap menjadi prioritas utama berikutnya.

---

## Item Baru Yang Diusulkan

Tidak ada item baru yang diusulkan masuk ke roadmap aktif pada fase ini. Catatan peningkatan minor seperti BL-007 (Edit Mode) tetap berada di Backlog sebagai peningkatan fungsionalitas di masa mendatang.

---

## Rekomendasi Roadmap Aktif

```
✅ Iteration 1:   Verifikasi Perjalanan              [SELESAI]
✅ Iteration 2:   Visibility Rule                    [SELESAI]
✅ Iteration 3-A: Verifikasi Temuan (Inti)           [SELESAI]
✅ Iteration 3-B: Pengayaan Kategori Temuan          [SELESAI]
✅ Iteration 4:   Reorientasi UI Observation-Centric [SELESAI — CLEAN]
🔜 Iteration 5:   Heatmap Perjalanan                 [TARGET SEKARANG]
📋 Iteration 6:   Manajemen Kategori Master          [DIRENCANAKAN]
📋 Iteration 7:   Heatmap Temuan                     [DIRENCANAKAN]
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan   [DIRENCANAKAN]
```

---

## Keputusan

1.  **Konfirmasi Iterasi 5**: Tetapkan "Heatmap Perjalanan" sebagai target implementasi untuk Iterasi berikutnya.
2.  **Pertahankan Backlog**: Biarkan item BL-001, BL-002, dan BL-009 tetap berada di backlog hingga prasyarat migrasi/koordinasi mobile disetujui.
