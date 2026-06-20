# Roadmap Review — Pasca Sprint Iterasi 01 & 02

> Dibuat pada: 2026-06-21
> Berdasarkan: Iterasi 01 (Verifikasi Perjalanan) dan Iterasi 02 (Visibility Rule)

---

## Ringkasan Evaluasi

Dua iterasi pertama telah berhasil membangun **fondasi proses verifikasi** yang menjadi tulang punggung dari keseluruhan roadmap. Sistem kini telah memiliki:
- Mekanisme operator memvalidasi data lapangan.
- Pemisahan yang jelas antara data mentah dan data sah.
- Halaman antrian verifikasi terpusat.

Namun, pengujian nyata mengungkap dua kebutuhan yang **belum ada di roadmap awal** dan perlu segera diselesaikan sebelum fitur berikutnya dibangun di atasnya: penyimpanan catatan penolakan dan perbaikan UX baris `rejected`.

Selain itu, **roadmap awal masih relevan secara keseluruhan**, namun urutan dan konten beberapa iterasi perlu disesuaikan berdasarkan pembelajaran yang diperoleh.

---

## Prioritas Yang Berubah

### Sebelum Review
```
Iteration 3: Verifikasi Temuan
Iteration 4: Heatmap Perjalanan
```

### Sesudah Review
Iterasi 2.5 disisipkan sebelum Iterasi 3 untuk menyelesaikan risiko aktif dari proses verifikasi perjalanan. Ini penting karena **Iterasi 3 (Verifikasi Temuan) akan membangun pola yang sama** — jika pola dari Iterasi 1-2 masih memiliki celah UX, pola yang sama akan terbawa ke Iterasi 3.

---

## Item Baru Yang Diusulkan

### Iterasi 2.5 — Perbaikan Alur Rejected
**Latar belakang:** Muncul dari feedback QA Iterasi 02.

**Isi:**
- Sembunyikan tombol aksi di baris `rejected`.
- Tampilkan catatan alasan penolakan di baris `rejected`.
- Pastikan catatan alasan tersimpan permanen.

**Keputusan bisnis terkait:** Perjalanan `rejected` **boleh** direvisi oleh pengguna mobile melalui resync. Operator cukup memberikan catatan alasan agar pengguna tahu apa yang perlu diperbaiki.

**Dampak pada roadmap:** Kecil. Hanya menyentuh `VerificationController` dan view `/verifications`. Tidak ada perubahan arsitektur.

---

## Item Yang Ditunda

**Tidak ada item yang ditunda dari roadmap awal.**

Semua item Iterasi 3–7 tetap dalam antrian dengan prioritas yang sama.

---

## Item Yang Dihapus

**Tidak ada item yang dihapus dari roadmap awal.**

---

## Rekomendasi Roadmap Baru

```
✅ Iteration 1:   Verifikasi Perjalanan          [SELESAI]
✅ Iteration 2:   Visibility Rule                [SELESAI]
🔄 Iteration 2.5: Perbaikan Alur Rejected        [AKTIF]
📋 Iteration 3:   Verifikasi Temuan              [DIRENCANAKAN]
📋 Iteration 4:   Heatmap Perjalanan             [DIRENCANAKAN]
📋 Iteration 5:   Kategori Temuan                [DIRENCANAKAN]
📋 Iteration 6:   Heatmap Temuan                 [DIRENCANAKAN]
📋 Iteration 7:   Pelaporan dan Statistik        [DIRENCANAKAN]
```

### Catatan Strategis

**Iterasi 3 — Verifikasi Temuan** akan mendapat manfaat langsung dari pola yang dibangun di Iterasi 1-2:
- `VerificationController` dapat diperluas untuk menangani `ActivityEvent`.
- Pola tabular view yang sama bisa digunakan untuk antrian verifikasi temuan.
- Visibility Rule untuk temuan sudah sebagian ada di `FindingController` (filter berdasarkan session `verified`).

**Iterasi 4 — Heatmap Perjalanan** memerlukan data perjalanan yang `verified` — sehingga Visibility Rule dari Iterasi 2 sudah menjadi prasyarat yang terpenuhi.

**Risiko sistemik yang belum diselesaikan roadmap:** `SyncController` yang selalu me-reset status ke `submitted`. Ini adalah keterbatasan teknis yang perlu dievaluasi secara terpisah di masa depan — bukan sebagai bagian dari iterasi fitur, tetapi sebagai **perbaikan infrastruktur** yang mungkin memerlukan koordinasi dengan tim mobile.

---

## Keputusan

- **Revisi roadmap** — Tambahkan Iterasi 2.5 sebelum Iterasi 3.
- **Lanjut roadmap** — Iterasi 3–7 tetap relevan dan tidak memerlukan perubahan urutan.
