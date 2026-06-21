# Walkthrough: Iterasi 04 (Reorientasi UI Observation-Centric)

Iterasi 04 bertujuan mentransformasi LILA UI dari *Tracking-Centric* menjadi **Observation-Centric**. Hal ini dilakukan dengan menggeser penekanan metrik dari jarak & durasi menjadi **Jumlah Temuan & Foto**.

## Perubahan yang Dilakukan

### 1. Dashboard
1. **Kotak Ringkasan (Summary Cards):** Urutan kartu di `/dashboard` kini menampilkan metrik **Temuan** dan **Foto** di urutan teratas, menggeser **Total Perjalanan** dan **Jarak Tempuh**.
2. **Grafik Tren 7 Hari:** Grafik batang yang sebelumnya menampilkan akumulasi *Jarak Tempuh* per hari kini mengukur fluktuasi **Jumlah Temuan** (*events_count*). Label dan proporsi batang pun kini dikalkulasi berbasis `maxTrendEvents`.
3. **Widget Pahlawan (Hero Widget):** Tidak lagi menyorot "Perjalanan Terjauh". Widget ini kini dirancang ulang untuk menyorot **Perjalanan Terkaya Temuan**, yaitu sesi penjelajahan dengan jumlah temuan terverifikasi terbanyak.

### 2. Daftar Perjalanan (Activities)
1. **Urutan Summary Cards:** Sama dengan di Dashboard, kartu metrik Temuan dan Foto diprioritaskan di daftar paling kiri.
2. **Filter Pencarian Baru:** Terdapat *checkbox toggle* bernama **Hanya ada temuan** di form filter. Jika dicentang, daftar perjalanan secara otomatis menyaring sesi yang sama sekali tidak membuahkan temuan terverifikasi (`has_findings`).
3. **Opsi Pengurutan:** Opsi penyortiran telah ditata ulang; **Temuan terbanyak** kini menjadi primadona yang menduduki urutan teratas (tepat di bawah opsi "Terbaru").
4. **Kalibrasi Ulang Kepadatan Data:** Bar kecil (*progress bar*) di bagian bawah setiap kartu perjalanan kini tidak lagi hanya menggunakan persentase absolut titik trek. Formula baru mengalikan bobot temuan dan foto (serta normalisasi adaptif terhadap nilai tertinggi dalam satu halaman) agar sejalan dengan paradigma *Observation-Centric*.

## Panduan Verifikasi Manual

1. Buka halaman **[Dashboard](http://lila.test/dashboard)**: Perhatikan pergeseran tajuk pada Summary Cards. Amati Widget *Hero* dan pastikan grafik 7 Hari mencetak metrik *temuan*.
2. Buka halaman **[Daftar Perjalanan](http://lila.test/activities)**:
    - Tes mencentang *checkbox* `Hanya ada temuan`. Pastikan hasil pencarian responsif.
    - Telusuri Opsi Urutkan (*Dropdown Sort*) untuk mendemonstrasikan prioritas baru.

Semua *logic query* telah diperbarui pada `DashboardController.php` dan `ActivityController.php` tanpa ada relasi API *Mobile* yang terputus.
