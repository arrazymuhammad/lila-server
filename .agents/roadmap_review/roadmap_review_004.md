# Roadmap Review 004
## LILA Web Application

> Tanggal: 2026-06-21
> Fase: Pasca Iterasi 05 — Pra-Iterasi 06

---

## 1. Evaluasi Urutan Iterasi

Berdasarkan `sprint_review.md` Iterasi 05 dan kondisi sistem saat ini:

**Iterasi 05 (Heatmap Perjalanan)** selesai bersih — satu file diubah, tidak ada bug, tidak ada regresi. Fitur heatmap aktif di `/map`.

Urutan roadmap berikutnya:

```
✅ Iteration 5:   Heatmap Perjalanan              [SELESAI — CLEAN]
📋 Iteration 6:   Manajemen Kategori Master       [TARGET BERIKUTNYA]
📋 Iteration 7:   Heatmap Temuan                  [DIRENCANAKAN]
📋 Iteration 8:   Pelaporan dan Statistik Lanjutan [DIRENCANAKAN]
```

**Kesimpulan:** Urutan iterasi berikutnya masih tepat.

---

## 2. Evaluasi Backlog Mendesak

### BL-009 — Manajemen Kategori Master (Sedang Mendesak)

Audit kode aktual mengkonfirmasi kondisi berikut:

- [`Verification/FindingController::review()`](../../app/Http/Controllers/Verification/FindingController.php) mengambil auto-suggest kategori langsung dari kolom `operator_category` via `DISTINCT` query — tidak ada validasi terhadap master list.
- Setiap operator dapat mengetik kategori baru secara bebas, termasuk ejaan yang berbeda untuk konsep yang sama (misal: "Jaring Ilegal" vs "jaring ilegal" vs "Jaring ilegal").
- Data `operator_category` yang kotor akan merusak analisis statistik kategori dan Heatmap Temuan (Iterasi 07) yang bergantung pada kategori yang bersih.

BL-009 sudah dijadwalkan sebagai Iterasi 06. Audit mengkonfirmasi urgensinya — **tidak ada alasan untuk menundanya lebih lanjut**.

### BL-001 — Rejected Reason Persistence (High, Pending)

Masih memerlukan perubahan schema database → belum ada keputusan → tidak menggusur iter 06.

### BL-002 — SyncController Status Reset (High, Pending)

Masih memerlukan koordinasi API mobile → tidak menggusur iter 06.

**Kesimpulan:** Tidak ada item backlog yang memaksa untuk menyela roadmap. Iterasi 06 adalah pilihan terbaik.

---

## 3. Risiko Aktif

| # | Risiko | Tingkat | Dampak pada Roadmap |
|---|--------|---------|---------------------|
| 1 | Data `operator_category` terus bertambah tanpa master → pembersihan makin berat | Sedang | Memperkuat urgensi Iterasi 06 |
| 2 | Heatmap Temuan (Iterasi 07) bergantung pada kualitas kategori | Sedang | Iterasi 06 harus selesai sebelum Iterasi 07 dimulai |
| 3 | BL-001/BL-002 menunggu keputusan infra | Sedang | Tidak mengubah prioritas saat ini |
| 4 | BL-010 Relevansi Progress Bar | Rendah | Kandidat evaluasi di Iterasi 08 |

**Kesimpulan:** Risiko aktif memperkuat posisi Iterasi 06 di roadmap, tidak ada yang mengubah prioritas.

---

## 4. Keputusan Final

- **Lanjutkan roadmap tanpa perubahan urutan.**
- **Iterasi 06 — Manajemen Kategori Master** dikonfirmasi sebagai iterasi berikutnya.
- Iterasi 06 memerlukan keputusan schema database (tabel `finding_categories` baru) — ini harus dikonfirmasi sebelum implementasi dimulai.
- Jika keputusan schema ditunda, scope Iterasi 06 dapat dibatasi hanya pada UI manajemen dengan backend yang masih membaca dari `DISTINCT operator_category` (model tanpa tabel master), lalu migrasi ke tabel master dilakukan di sub-iterasi berikutnya.
