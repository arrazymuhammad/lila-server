# QA Checklist
## Iterasi 07 — Heatmap Temuan Berdasarkan Kategori

> Dibuat berdasarkan: `iteration.md`, `implementation_report.md`, `walkthrough.md`
> URL aplikasi: http://lila.test

---

## Functional Test

### 1. Toggle Layer Heatmap Temuan
- [ ] Buka halaman `/map`. Pastikan halaman memuat peta utama.
- [ ] Aktifkan toggle **Heatmap Temuan** (warna ungu) → pastikan marker temuan individual (circleMarker) hilang dan digantikan oleh visualisasi densitas heatmap (spektrum warna ungu/merah/kuning).
- [ ] Matikan toggle **Heatmap Temuan** → pastikan layer heatmap hilang dan marker bulat individual kembali muncul.
- [ ] Aktifkan **Heatmap Temuan** dan **Heatmap Rute** bersamaan → pastikan kedua layer heatmap (rute & temuan) ter-render bersamaan di peta tanpa error.

### 2. Filter Kategori Temuan
- [ ] Aktifkan toggle **Heatmap Temuan**. Pastikan dropdown **Kategori Temuan** muncul secara dinamis di samping form bulan/tahun.
- [ ] Pilih salah satu kategori master (misal: "Alat Tangkap Terlarang") → pastikan heatmap ter-refresh secara real-time (tanpa reload halaman) dan hanya merender panas densitas di titik temuan yang memiliki kategori tersebut.
- [ ] Matikan toggle **Heatmap Temuan** → pastikan dropdown **Kategori Temuan** kembali disembunyikan.
- [ ] Pilih "Semua Kategori" di dropdown → pastikan peta merender seluruh data temuan kembali.

### 3. Persistensi State
- [ ] Aktifkan toggle **Heatmap Temuan**.
- [ ] Pindah periode bulan/tahun menggunakan filter lalu tekan **Terapkan** → pastikan halaman me-reload dan toggle **Heatmap Temuan** tetap aktif secara otomatis (membaca dari `localStorage`).

---

## UI Test
- [ ] Toggle switch Heatmap Temuan berwarna ungu (`bg-purple-600`) untuk membedakannya secara visual dari Heatmap Rute yang berwarna merah (`bg-rose-600`).
- [ ] Dropdown filter kategori memiliki styling background ungu muda (`bg-purple-50`) untuk mengindikasikan keterikatannya dengan filter heatmap temuan.
- [ ] Dropdown filter kategori disembunyikan secara bersih (`display: none` atau `x-show`) ketika toggle Heatmap Temuan dimatikan.
- [ ] Peta membatasi zoom heatmap temuan (`maxZoom: 15`) agar titik densitas tidak pecah/blur berlebih ketika zoom in maksimal.

---

## Regression Test
- [ ] Halaman peta utama `/map` dapat dimuat tanpa error JavaScript di console developer tools.
- [ ] Heatmap Perjalanan / Rute (Iterasi 05) tetap berfungsi normal (densitas trackpoints ter-render dengan baik).
- [ ] Marker detail popup untuk temuan individual (saat heatmap dinonaktifkan) masih dapat diklik dan menampilkan popup dengan link detail temuan secara benar.
- [ ] Detail Perjalanan (`/activities/{session}`) tetap bisa memuat peta perjalanan lokal tanpa error.

---

## Acceptance Criteria
- [ ] Ektraksi data `operator_category` dari backend (`MapController`) tersaji di client-side payload.
- [ ] Pengambilan master kategori dari tabel `finding_categories` terintegrasi di controller & view peta.
- [ ] Kemampuan filter densitas temuan per kategori bekerja real-time di client-side.
- [ ] Desain non-destructive (tidak ada perubahan API sinkronisasi mobile atau schema DB).
