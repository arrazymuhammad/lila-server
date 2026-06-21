# QA Checklist
## Iterasi 04 — Reorientasi UI Observation-Centric

> Dibuat berdasarkan: `iteration.md`, `implementation_report.md`, `walkthrough.md`
> URL aplikasi: http://lila.test

---

## Functional Test

### Dashboard — Grafik Tren 7 Hari

- [x] Buka `/dashboard`. Grafik tren 7 hari menampilkan label bertema **temuan** (bukan "Jarak" atau "km").
- [x] Sumbu Y / tinggi batang grafik berubah sesuai jumlah temuan per hari (bukan jarak tempuh).
- [x] Jika tidak ada temuan terverifikasi dalam 7 hari terakhir, grafik menampilkan semua batang bernilai nol tanpa error halaman.
- [x] Normalisasi batang grafik berjalan benar: batang hari dengan temuan terbanyak mendekati tinggi penuh.

### Dashboard — Widget Hero (Highlight Session)

- [x] Widget Hero di `/dashboard` menampilkan perjalanan dengan **jumlah temuan terbanyak** (bukan jarak terjauh).
- [x] Jika semua perjalanan memiliki `events_count = 0`, widget Hero menampilkan kondisi fallback yang informatif (pesan atau disembunyikan) — tidak crash.
- [x] Data yang ditampilkan di Widget Hero (judul, jumlah temuan, jarak, foto) akurat sesuai data di database.

### Dashboard — Urutan Summary Cards

- [x] Summary Cards di `/dashboard` menampilkan **Temuan** di posisi pertama (paling kiri / atas).
- [x] Summary Cards di `/dashboard` menampilkan **Foto** di posisi kedua.
- [x] Summary Cards di `/dashboard` menampilkan **Total Perjalanan** dan **Total Jarak** setelah Temuan & Foto.

### Daftar Perjalanan — Urutan Summary Cards

- [x] Summary Cards di `/activities` menampilkan **Temuan** di posisi pertama.
- [x] Summary Cards di `/activities` menampilkan **Foto** di posisi kedua.
- [x] Summary Cards di `/activities` menampilkan **Total Perjalanan** dan **Total Jarak** setelah Temuan & Foto.

### Daftar Perjalanan — Filter "Hanya perjalanan dengan temuan"

- [x] Tersedia elemen filter bertema **"Hanya ada temuan"** (checkbox atau toggle) di halaman `/activities`.
- [x] Mencentang filter tersebut menyebabkan halaman hanya menampilkan perjalanan yang memiliki minimal satu temuan terverifikasi.
- [x] Perjalanan tanpa temuan terverifikasi **tidak muncul** ketika filter aktif.
- [x] Menonaktifkan filter mengembalikan daftar lengkap semua perjalanan.
- [x] Filter dapat dikombinasikan dengan filter lain (pencarian nama, status) tanpa error.
- [x] State filter dipertahankan di URL query string sehingga link dapat dibagikan/di-refresh.

### Daftar Perjalanan — Sorting "Temuan Terbanyak"

- [x] Dropdown sort di `/activities` memiliki opsi **"Temuan terbanyak"**.
- [x] Opsi "Temuan terbanyak" berada di posisi yang menonjol (urutan atas dropdown).
- [x] Memilih "Temuan terbanyak" mengurutkan daftar dari perjalanan dengan temuan terbanyak ke sedikit.

### Daftar Perjalanan — Progress Bar Kartu

- [x] Progress bar di setiap kartu perjalanan mencerminkan bobot berbasis **temuan** dan **foto** (bukan murni jumlah track point).
- [x] Kartu perjalanan dengan temuan lebih banyak menampilkan progress bar yang lebih panjang dibanding kartu tanpa temuan (dalam kondisi data yang beragam).
- [x] Jika semua kartu dalam satu halaman memiliki `events_count = 0` dan `photos_count = 0`, progress bar tidak error (fallback berjalan, misalnya menggunakan track_points_count atau semua bar 0).

---

## UI Test

- [x] Perubahan urutan Summary Cards di `/dashboard` tidak menyebabkan layout rusak atau terpotong pada lebar layar desktop (≥ 1280px).
- [x] Perubahan urutan Summary Cards di `/activities` tidak menyebabkan layout rusak atau terpotong pada lebar layar desktop.
- [x] Label grafik tren di `/dashboard` terbaca jelas dan tidak tumpang tindih.
- [x] Widget Hero di `/dashboard` menampilkan informasi dengan layout yang rapi (tidak overflow atau terpotong).
- [x] Checkbox/toggle filter "Hanya ada temuan" di `/activities` terlihat konsisten dengan elemen filter lain di halaman yang sama.
- [x] Progress bar kartu perjalanan tidak overflow atau keluar dari batas kartu.
- [x] Dropdown sort menampilkan opsi "Temuan terbanyak" dengan label yang jelas (tidak terpotong).

---

## Regression Test

- [x] Halaman `/dashboard` dapat dibuka tanpa error 500.
- [x] Halaman `/activities` dapat dibuka tanpa error 500.
- [x] Halaman `/activities/{session}` (detail perjalanan) dapat dibuka tanpa error — tidak ada perubahan di halaman ini tapi perlu dikonfirmasi tidak terdampak.
- [x] Halaman `/findings` dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Halaman `/findings/{event}` (detail temuan) dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Halaman `/map` (peta semua rute) dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Filter pencarian nama (query `q`) di `/activities` masih berfungsi normal.
- [x] Filter status di `/activities` masih berfungsi normal.
- [x] Paginasi di `/activities` berfungsi benar (halaman 2, 3, dst. dapat dibuka).
- [x] Paginasi di `/activities` dengan filter aktif berfungsi benar.
- [x] Link dari kartu perjalanan di `/activities` ke halaman detail (`/activities/{session}`) berfungsi.
- [x] API `/api/sync` tidak terpengaruh — tidak ada perubahan pada controller atau model terkait sync.

---

## Acceptance Criteria

- [x] Grafik tren 7 hari di Dashboard menggunakan jumlah temuan sebagai sumbu Y *(ISS-001)*
- [x] Widget highlight/hero di Dashboard menampilkan perjalanan paling kaya temuan — bukan terjauh *(ISS-002)*
- [x] Summary cards di Dashboard diurutkan: Temuan → Foto → Total Perjalanan → Total Jarak *(ISS-003)*
- [x] Summary cards di Daftar Perjalanan diurutkan: Temuan → Foto → Total Perjalanan → Total Jarak *(ISS-003)*
- [x] Progress bar kartu perjalanan di `/activities` dihitung berdasarkan bobot temuan dan foto *(ISS-004)*
- [x] Tersedia filter "Hanya perjalanan dengan temuan" di Daftar Perjalanan *(ISS-005)*
- [x] Tidak ada perubahan pada API mobile atau data sinkronisasi
- [x] Tidak ada perubahan pada halaman `/map`, `/findings`, dan `/activities/{session}`

---

## Notes

1. **Grafik kosong bukan bug** — Jika belum ada temuan dengan status `verified`, grafik tren 7 hari akan menampilkan semua batang bernilai nol. Ini adalah kondisi yang diharapkan sesuai catatan di `iteration.md` (Risiko 7.1).
2. **Widget Hero fallback** — Jika semua session memiliki `events_count = 0`, perlu dikonfirmasi apakah widget disembunyikan atau menampilkan pesan informatif (sesuai Risiko B dari `review.md`).
3. **Target browser** — Uji pada Chrome terbaru. Aplikasi tidak didesain untuk browser lama.
4. **Data uji yang disarankan** — Gunakan database yang memiliki campuran: perjalanan dengan temuan terverifikasi, perjalanan dengan temuan tapi belum diverifikasi, dan perjalanan tanpa temuan sama sekali. Ini penting untuk menguji semua kondisi edge case.
5. **Progress bar** — Progress Bar dapat dihapus mengingat relevansi terhadap jarak dan jumlah track points yang tidak lagi menjadi metrik utama.  

## Bug Notes
