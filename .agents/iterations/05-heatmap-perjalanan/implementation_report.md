# Laporan Implementasi
## Iterasi 05: Heatmap Perjalanan

* Ringkasan Implementasi
Iterasi 05 berfokus pada integrasi visualisasi tingkat lanjut berwujud **Heatmap Perjalanan** pada halaman *Semua Rute* (`/map`). Implementasi ini memanfaatkan fungsionalitas murni di sisi *frontend* web dengan menyisipkan pustaka khusus berbasis Leaflet, mendayagunakan status dinamis dari AlpineJS, dan menyimpan persetensi memori melalui *localStorage* browser.

* File Yang Diubah
1. `resources/views/maps/index.blade.php` (Menambahkan tag inklusi CDN *leaflet-heat*, menciptakan antarmuka sakelar ganda, serta menyematkan logika percabangan render pada fungsi `allRoutesMap()` milik AlpineJS).

* Route Yang Ditambah
Tidak ada penambahan maupun manipulasi rute (`web.php`). 

* Fitur Yang Berhasil Diimplementasikan
1. Integrasi pustaka *leaflet-heat.js* dengan resolusi render `radius: 20` dan blur optis `15`.
2. Pengalihan cerdas (*toggle*) yang secara seketika menghapus tumpukan *polyline* standar dan mengekstrak semua susunan array koordinat menjadi *layer heatmap*.
3. Sistem penyimpanan status (*state persistance*) via kunci *localStorage* bernama `lila_show_heatmap` untuk memastikan peta tak memudar usai peramban diperbarui ulang (*refresh/reload*).

* Deviasi Dari Iterasi
Tidak ada deviasi kode dari mandat yang tercantum di `ITERATION_05.md`.

* Risiko Yang Masih Ada
1. Karena kalkulasi *array* intensitas dijalankan di DOM (sisi *client*), memuat ratusan rute secara bebarengan (tanpa penyaringan filter bulan/tahun) ke dalam kalkulator Heatmap mungkin akan menyebabkan perlambatan *render* sepersekian detik di gawai berspesifikasi minim.
2. Heatmap saat ini dikonfigurasi murni berdasarkan remah *track points* navigasi (garis rute) yang tercetak per detik; *Heatmap Temuan* terpisah (*Finding Heatmap*) baru akan dieksekusi di ranah iterasi berikutnya sesuai *roadmap*.
