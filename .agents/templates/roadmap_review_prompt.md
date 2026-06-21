# ROADMAP REVIEW PROMPT

Bertindaklah sebagai:

* Product Manager
* Business Analyst

## Dokumen Yang Harus Dibaca

1. AI_CONTEXT.md
2. SYSTEM_ANALYSIS.md
3. ROADMAP.md

Kemudian baca seluruh iterasi yang sudah selesai:

* final_report.md
* sprint_review.md

## Tujuan

Evaluasi apakah roadmap saat ini masih relevan.

## Evaluasi

1. Apakah tujuan roadmap masih sesuai?
2. Apakah ada kebutuhan baru yang muncul?
3. Apakah ada prioritas yang berubah?
4. Apakah ada item roadmap yang tidak lagi penting?
5. Apakah ada risiko yang mengubah arah pengembangan?

## Output

Simpan file di folder:

.agents/roadmap_review/

Nama file menggunakan format nomor inkremental:

roadmap_review_001.md
roadmap_review_002.md
roadmap_review_003.md

Gunakan nomor yang belum dipakai (cek isi folder terlebih dahulu sebelum membuat file baru).

Berisi:

* Ringkasan Evaluasi
* Prioritas Yang Berubah
* Item Baru Yang Diusulkan
* Item Yang Ditunda
* Item Yang Dihapus
* Rekomendasi Roadmap Baru

Jika diperlukan, buat versi revisi ROADMAP.md:

ROADMAP_V{incremental}.md di folder .agents/roadmap_review/

## Contoh Nama File

roadmap_review/roadmap_review_001.md  ← review pertama (pasca Iterasi 01-02)
roadmap_review/roadmap_review_002.md  ← review kedua (pasca Iterasi 03)
roadmap_review/roadmap_review_003.md  ← review ketiga (dst)

## Catatan

Setiap file roadmap_review adalah catatan historis permanen.
Jangan menimpa file roadmap_review yang sudah ada.
Selalu buat file baru dengan nomor inkremental berikutnya.
