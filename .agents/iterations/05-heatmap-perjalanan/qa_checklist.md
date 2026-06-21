# QA Checklist
## Iterasi 05 — Heatmap Perjalanan

> Dibuat berdasarkan: `iteration.md`, `implementation_report.md`, `walkthrough.md`
> URL aplikasi: http://lila.test

---

## Functional Test

### Toggle Heatmap — Aktivasi

- [x] Buka `/map`. Toggle berlabel **Heatmap** tersedia di header panel kontrol peta.
- [x] Toggle Heatmap dalam kondisi **OFF** secara default (kecuali sebelumnya pernah diaktifkan).
- [x] Klik toggle Heatmap → layer polyline garis rute **menghilang** dari peta.
- [x] Klik toggle Heatmap → layer **heatmap** (gradasi warna densitas) muncul menggantikan polyline.
- [x] Area yang dilintasi banyak track points menampilkan warna panas (merah/kuning).
- [x] Area yang jarang dilintasi menampilkan warna dingin (biru/hijau) atau kosong.
- [x] Heatmap dirender dari akumulasi **semua** track points seluruh perjalanan terverifikasi pada periode terpilih.

### Toggle Heatmap — Deaktivasi

- [x] Klik toggle Heatmap kembali (OFF) → layer heatmap **menghilang** dari peta.
- [x] Klik toggle Heatmap kembali (OFF) → layer polyline garis rute **kembali muncul** dengan warna masing-masing perjalanan.
- [x] Popup detail perjalanan pada polyline dapat diklik dan menampilkan informasi yang benar (judul, tanggal, jarak, link detail).

### State Persistensi localStorage

- [x] Aktifkan Heatmap → refresh halaman → Heatmap **tetap aktif** (toggle dalam posisi ON, heatmap dirender).
- [x] Matikan Heatmap → refresh halaman → mode polyline **tetap aktif** (toggle dalam posisi OFF, polyline dirender).
- [x] Key localStorage yang digunakan adalah `lila_show_heatmap` (dapat diperiksa via DevTools → Application → Local Storage).

### Interaksi Heatmap + Toggle Temuan

- [x] Aktifkan Heatmap + aktifkan **Semua Temuan** → marker temuan (termasuk yang belum diverifikasi / abu-abu) **tetap tampil** di atas heatmap.
- [x] Aktifkan Heatmap + matikan **Semua Temuan** → hanya marker temuan **verified** yang tampil di atas heatmap.
- [x] Toggle **Semua Temuan** berfungsi normal terlepas dari state Heatmap (ON/OFF).

### Filter Bulan & Tahun + Heatmap

- [x] Ganti filter bulan/tahun → klik Terapkan → heatmap **diperbarui** sesuai data periode baru.
- [x] Jika tidak ada perjalanan terverifikasi pada periode terpilih → heatmap **kosong** (tidak ada layer heatmap) dan muncul notice "Belum ada rute atau koordinat temuan pada periode ini."
- [x] Filter bulan/tahun tidak mengubah state toggle Heatmap (toggle tetap pada posisi terakhir sebelum filter diterapkan, karena diambil dari localStorage).

### Edge Case

- [x] Jika hanya satu perjalanan dengan track points sedikit (< 5 titik) → heatmap tetap dirender meski tipis, tidak error.
- [x] Jika semua perjalanan tidak memiliki track points (coordinates kosong) → tidak ada heatmap dirender, peta menampilkan view default Indonesia.

---

## UI Test

- [x] Toggle Heatmap memiliki label teks **"Heatmap"** yang terbaca jelas.
- [x] Toggle Heatmap berubah warna saat aktif: **merah/rose** saat ON, abu-abu saat OFF — konsisten dengan desain toggle yang sudah ada.
- [x] Toggle Heatmap terletak **di sebelah kiri** toggle "Semua Temuan" (urutan: Heatmap | Semua Temuan | filter bulan/tahun).
- [x] Layout header panel kontrol peta tidak rusak pada lebar layar desktop (≥ 1280px) setelah penambahan toggle baru.
- [x] Toggle Heatmap dan toggle Semua Temuan tidak tumpang tindih atau saling menutupi pada lebar layar sedang (1024px).
- [x] Transisi toggle (animasi pergeseran bulatan putih) berjalan mulus.
- [x] Leaflet heatmap tidak menghasilkan artefak visual (misalnya heatmap menimpa tile peta dengan cara yang aneh).

---

## Regression Test

- [x] Halaman `/map` dapat dibuka tanpa error 500.
- [x] Halaman `/map` memuat peta Leaflet (tile OpenStreetMap) dengan normal.
- [x] Library `leaflet-heat.js` dimuat via CDN tanpa error di console browser (periksa DevTools → Console).
- [x] Mode polyline standar (Heatmap OFF) berfungsi identik dengan sebelum iterasi ini — garis rute berwarna, popup klik, fit bounds.
- [x] Toggle **Semua Temuan** berfungsi normal (tidak terdampak penambahan toggle Heatmap).
- [x] Halaman `/activities` dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Halaman `/activities/{session}` (detail perjalanan) dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Halaman `/findings` dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] Halaman `/dashboard` dapat dibuka tanpa error — tidak ada perubahan di halaman ini.
- [x] API `/api/sync` tidak terpengaruh — tidak ada perubahan pada controller atau model.
- [x] `MapController.php` tidak mengalami perubahan (konfirmasi via git diff atau inspeksi manual).

---

## Acceptance Criteria

- [x] Library `leaflet-heat.js` termuat via CDN tanpa error di halaman `/map`.
- [x] Toggle berlabel "Heatmap" tersedia dan berfungsi di halaman `/map`.
- [x] Mengaktifkan toggle → polyline rute menghilang, layer heatmap densitas muncul.
- [x] Mematikan toggle → heatmap menghilang, polyline rute standar kembali tampil.
- [x] Marker temuan tetap dapat ditampilkan/disembunyikan via toggle "Semua Temuan" terlepas dari state Heatmap.
- [x] State pilihan toggle tersimpan di `localStorage` dan dimuat ulang dengan benar saat refresh halaman.
- [x] Filter bulan/tahun tetap berfungsi memperbarui data heatmap.
- [x] Tidak ada error baru di log Laravel (`storage/logs/laravel.log`) maupun console browser.
- [x] Tidak ada perubahan pada API mobile atau kontrak sinkronisasi.
- [x] Tidak ada perubahan pada halaman `/activities`, `/findings`, dan `/dashboard`.

---

## Notes

1. **Heatmap kosong bukan bug** — Jika tidak ada perjalanan terverifikasi pada periode terpilih, atau semua perjalanan tidak memiliki track points, heatmap tidak dirender. Ini kondisi yang diharapkan.
2. **Target browser** — Uji pada Chrome terbaru. Leaflet dan leaflet-heat tidak didesain untuk browser lama.
3. **Data uji yang disarankan** — Gunakan periode yang memiliki minimal 3–5 perjalanan terverifikasi dengan track points untuk memverifikasi efek gradasi densitas heatmap.
4. **CDN dependency** — `leaflet-heat.js` dimuat dari `unpkg.com`. Jika koneksi internet tidak tersedia di lingkungan uji, library tidak akan termuat dan heatmap akan gagal dengan error `L.heatLayer is not a function`. Ini adalah risiko yang sudah didokumentasikan di `iteration.md` (Risiko #2) dan diterima untuk iterasi ini.
5. **localStorage inspection** — Untuk memverifikasi persistensi, buka DevTools → Application → Local Storage → `http://lila.test`. Periksa key `lila_show_heatmap` bernilai `"true"` saat toggle ON dan `"false"` saat OFF.

## Bug Notes
