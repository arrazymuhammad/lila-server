# Sprint Review: Iterasi 08 - Rich Finding Popup

## Demo & Validasi
- **Fitur Baru**: Card popup pada marker temuan menampilkan foto utama (carousel jika >1 foto), judul temuan, kategori temuan, deskripsi singkat (line-clamp), metadata waktu, serta badge status.
- **Interaksi**: Navigasi tombol prev/next berputar secara sirklik, counter berubah (`1/3` ke `2/3`, dst).
- **Regresi**: Heatmap rute, heatmap temuan, filter kategori, dan pencarian perjalanan di sisi kanan tetap berjalan normal.

## Feedback & Keputusan
1. Parser JSON string pada parameter `data-photos` memerlukan single quote `'` di level atribut HTML agar aman saat membaca `"`. (Telah diselesaikan).
2. Helper URL berkas diselaraskan memakai `url(...)` seperti template detail temuan. (Telah diselesaikan).

## Status Backlog
- Iterasi 08 dinyatakan **SELESAI (DONE)**.
- Seluruh artefak implementasi, laporan, checklist, dan review telah terdokumentasi dengan lengkap.
