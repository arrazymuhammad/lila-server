# Walkthrough: Iterasi 07 (Heatmap Temuan Berdasarkan Kategori)

LILA kini memiliki kemampuan visualisasi spasial yang lebih dalam. Operator tidak hanya bisa melihat di mana petugas berpatroli (Heatmap Rute), tetapi juga melihat konsentrasi temuan secara spesifik berdasarkan kategori (Heatmap Temuan).

Berikut adalah fitur yang baru ditambahkan dan cara kerjanya:

---

### 1. Toggle "Heatmap Temuan"
Di halaman Peta (`/map`), panel kontrol atas sekarang memiliki *tiga* toggle:
1. **Heatmap Temuan** (Baru - Ungu)
2. **Heatmap Rute** (Iterasi 05 - Merah)
3. **Semua Temuan**

Mengaktifkan **Heatmap Temuan** akan mengubah titik-titik temuan (yang sebelumnya berupa lingkaran ber-popup) menjadi layer awan densitas. Warna densitas ini menggunakan spektrum *purple-fuchsia-red-yellow* untuk membedakannya dari Heatmap Rute (yang berwarna *blue-lime-red*).

### 2. Dropdown Filter "Kategori Temuan"
Ketika toggle Heatmap Temuan **dinyalakan**, sebuah dropdown `<select>` baru akan muncul secara otomatis di sebelah kiri pilihan "Bulan".

Dropdown ini membaca langsung dari master data `finding_categories` (hasil Iterasi 06).
- Secara *default*, opsi yang terpilih adalah "Semua Kategori", yang menampilkan konsentrasi seluruh temuan.
- Jika pengguna memilih kategori spesifik (misal: "Alat Tangkap Terlarang"), peta akan langsung *me-refresh* (tanpa reload halaman penuh) dan hanya menampilkan awan densitas di titik-titik temuan yang masuk dalam kategori tersebut.

### 3. Integrasi Non-Destruktif & Persistensi
Perubahan ini diatur 100% menggunakan AlpineJS di sisi browser. 
- Pilihan pengguna untuk menyalakan Heatmap Temuan akan tersimpan di browser (`localStorage`), sehingga saat berpindah bulan atau tahun, fitur tetap aktif.
- Pengguna dapat menyalakan **Heatmap Rute** dan **Heatmap Temuan** secara bersamaan untuk melihat korelasi antara "lokasi paling sering dilewati" dengan "lokasi paling banyak temuan".

---

## Cara Menguji (Manual QA)

Ikuti langkah-langkah berikut untuk memvalidasi implementasi:

1. Pastikan Anda memiliki beberapa data master di `/categories`.
2. Buka halaman `/map`.
3. Klik toggle **Heatmap Temuan** → titik marker individual hilang, digantikan awan warna keunguan/kemerahan.
4. Perhatikan dropdown **Kategori Temuan** muncul di panel atas.
5. Pilih salah satu kategori di dropdown → awan heatmap akan menyusut/berubah, hanya menampilkan titik temuan dengan kategori yang dipilih.
6. Klik toggle **Heatmap Rute** (dinyalakan bersamaan) → Anda akan melihat dua awan warna yang berbeda (satu untuk kepadatan jalur, satu untuk kepadatan masalah).
7. Ganti "Bulan" ke bulan lain lalu tekan Terapkan → pastikan status toggle tetap menyala.
