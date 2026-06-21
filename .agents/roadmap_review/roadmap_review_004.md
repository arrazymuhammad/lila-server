# Roadmap Review 004
## LILA Web Application

> Tanggal: Hari ini
> Fase: Pra-Iterasi 07

---

## 1. Evaluasi Urutan Iterasi

Berdasarkan `sprint_review.md` Iterasi 06 dan feedback QA yang sudah didokumentasikan, roadmap berjalan sesuai rencana.

Iterasi 06 (Manajemen Kategori Master) berhasil diselesaikan penuh:
- Master data `finding_categories` tersedia.
- CRUD `/categories` berfungsi.
- Auto-suggest di form verifikasi menggunakan master.
- Filter daftar temuan berbasis master.

Kategori data yang sebelumnya *free-text* kini berpotensi distandardisasi. Ini membuat Iterasi 07 (Heatmap Temuan Berdasarkan Kategori) memiliki fondasi data yang jauh lebih kuat dari sebelumnya.

Urutan roadmap saat ini:

```
✅ Iteration 1:   Verifikasi Perjalanan
✅ Iteration 2:   Visibility Rule
✅ Iteration 3-A: Verifikasi Temuan (Inti)
✅ Iteration 3-B: Pengayaan Kategori Temuan
✅ Iteration 4:   Reorientasi UI Observation-Centric
✅ Iteration 5:   Heatmap Perjalanan
✅ Iteration 6:   Manajemen Kategori Master
🔜 Iteration 7:   Heatmap Temuan Berdasarkan Kategori  ← TARGET
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan
```

**Kesimpulan:** Urutan iterasi berikutnya masih tepat dan relevan.

---

## 2. Evaluasi Backlog Mendesak

| ID | Item | Prioritas | Relevansi ke Iter 07 |
|----|------|-----------|----------------------|
| BL-001 | Rejected Reason Persistence | High | Tidak — memerlukan schema DB. Tetap ditangguhkan. |
| BL-002 | SyncController Status Reset | High | Tidak — memerlukan koordinasi mobile. Tetap ditangguhkan. |
| BL-007 | Edit Mode Temuan Verified | Medium | Relevan pasca Iter 06 — operator butuh koreksi kategori lama. Naik ke prioritas Medium-High. Kandidat Iterasi 08. |
| BL-008 | Auto-suggest Alphabetic | Low | Sudah **Resolved** otomatis oleh Iterasi 06 (auto-suggest membaca dari tabel master yang sudah alpha-sorted). |
| BL-010 | Evaluasi Progress Bar | Low | Tidak mendesak. Tetap kandidat Iterasi 08. |

Tidak ada backlog yang mendesak cukup untuk menyela roadmap Iterasi 07.

**Kesimpulan:** Tidak ada perubahan urutan roadmap. Iterasi 07 dikonfirmasi sebagai berikutnya.

---

## 3. Risiko Aktif

- **Data kategori lama (free-text) mungkin tidak cocok dengan nama master:** Temuan yang diverifikasi sebelum Iterasi 06 menyimpan `operator_category` sebagai string bebas. Heatmap per kategori di Iterasi 07 akan mengelompokkan berdasarkan string ini — tidak ada normalisasi ke tabel master. Ini adalah batasan yang diterima dan harus dijelaskan secara eksplisit di `iteration.md`.
- **Volume data temuan berkoordinat terbatas:** Heatmap hanya bekerja jika ada cukup data dengan `latitude`/`longitude` yang valid di `activity_events`. Perlu dikomunikasikan ke pengguna bahwa heatmap berbasis temuan bergantung pada kualitas input GPS dari mobile.
- **BL-007 (Edit Mode):** Urgensinya meningkat pasca Iterasi 06. Perlu dievaluasi saat persiapan Iterasi 08.

---

## Keputusan Final

**Lanjutkan Iterasi 07 — Heatmap Temuan Berdasarkan Kategori.**

BL-008 dicatat sebagai Resolved secara otomatis oleh Iterasi 06. BL-007 dinaikkan ke Medium-High untuk dipertimbangkan di Iterasi 08.
