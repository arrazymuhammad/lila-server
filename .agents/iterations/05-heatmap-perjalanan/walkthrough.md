# Walkthrough: Iterasi 05 (Heatmap Perjalanan)

Iterasi 05 menghadirkan fitur visualisasi tingkat lanjut untuk peta operasional LILA: **Heatmap Perjalanan**.

## Fitur Baru

### 1. Toggle Heatmap di Peta Utama
Di halaman `/map`, sekarang terdapat opsi sakelar (*toggle switch*) berlabel **Heatmap**. Sakelar ini diletakkan bersanding dengan filter "Semua Temuan" pada bagian kendali panel peta (kanan atas).

### 2. Rendering Berbasis Densitas
Ketika **Heatmap** dihidupkan, peta tidak lagi menggambar seluruh rentetan garis (*polyline*) perjalanan. Sebaliknya, peta mengakumulasikan seluruh titik trek geografis (*track points*) menjadi formasi "awan panas".
- Area dengan lintasan patroli bertumpang tindih tinggi akan menyala merah (Hotspot).
- Area pinggiran yang jarang dilalui menampilkan spektrum biru-hijau.
- Mode ini membersihkan antarmuka peta dari garis-garis ruwet (*visual noise*), dan menyajikan potret instan wilayah liputan operator lapangan.

### 3. Integrasi Non-Destruktif
Heatmap dijamin tidak merusak (*non-destructive*) fitur lainnya:
- Pin marker temuan (titik merah/abu-abu) akan tetap digambar meskipun Heatmap menyala.
- Setelan preferensi pengguna akan terekam oleh aplikasi (via *localStorage*). Jika Anda merefresh layar, peta otomatis mengingat apakah mode Heatmap dibiarkan hidup atau mati.

## Cara Menguji (Manual QA)

1. Pastikan Anda punya beberapa sesi perjalanan (Activities) dengan *track points* yang sudah tersinkronisasi.
2. Navigasikan *browser* ke halaman Peta (`/map`).
3. Klik *toggle* "Heatmap". Saksikan transisi instan dari segerombolan *polyline* berubah menjadi corak gradasi sebaran densitas rute.
4. Klik kembali *toggle* untuk mengembalikan mode garis rute klasik. Coba klik garis-garis rute untuk memunculkan kotak *popup* detailnya.
