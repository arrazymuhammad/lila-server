# Issue Analysis: 001
## Berdasarkan raw/001.md — UX Review oleh Gemini

**Tanggal Analisis:** 2026-06-21
**Sumber Feedback:** Gemini UX Review
**Jenis:** UX / UI Priority Review

---

## 1. Identifikasi Issue yang Valid

### ISS-001 — Dashboard Masih Tracking-Centric
**Kategori:** UX / UI Prioritization
**Urgensi:** Tinggi
**Dampak Bisnis:** Operator membaca dashboard dan melihat "jarak tempuh" sebagai metrik utama, padahal misi LILA adalah monitoring temuan. Ini bertentangan langsung dengan visi produk.

**Detail:**
- Metrik utama di atas fold: Total Jarak Tempuh dan rata-rata jarak — bukan temuan.
- Grafik utama: Tren 7 Hari berbasis Jarak (km) — bukan jumlah temuan.
- Widget "Perjalanan Terjauh" mempromosikan perjalanan dengan 0 temuan sebagai "pencapaian".
- Angka temuan dan foto diletakkan di posisi sekunder secara visual.

**Rekomendasi:** Tambahkan ke ISSUES.md dan jadikan kandidat Iterasi berikutnya (tinggi nilai bisnis, rendah risiko teknis).

---

### ISS-002 — Urutan Summary Cards di Daftar Perjalanan Bias ke Tracking
**Kategori:** UX / UI Prioritization
**Urgensi:** Sedang
**Dampak Bisnis:** Operator yang membaca halaman `/activities` secara visual diarahkan untuk memperhatikan jarak dan durasi terlebih dahulu, bukan temuan. Ini memperkuat kebiasaan lama (tracking-centric) dan memperlambat transisi ke observation-centric.

**Detail:**
- Urutan saat ini: Total Perjalanan → Total Jarak → Total Durasi → Temuan → Foto
- Seharusnya: Temuan → Foto → Total Perjalanan → Total Jarak

**Rekomendasi:** Tambahkan ke ISSUES.md. Dapat diselesaikan bersamaan dengan ISS-001.

---

### ISS-003 — Indikator Progress Bar Kartu Aktivitas Berbasis Track Point, Bukan Nilai Temuan
**Kategori:** UX / Visual Logic
**Urgensi:** Sedang
**Dampak Bisnis:** Bar biru pada kartu perjalanan saat ini mencerminkan panjang rute (track point) — bukan nilai pengamatan. Akibatnya, perjalanan 315 km tanpa temuan memiliki bar lebih panjang dari perjalanan 16 km dengan 3 temuan. Ini menyesatkan operator dalam menentukan perjalanan mana yang perlu dianalisis lebih dulu.

**Rekomendasi:** Tambahkan ke ISSUES.md. Logika kalkulasi perlu diubah — berikan bobot pengali untuk setiap temuan/foto yang ada.

---

### ISS-004 — Grafik Tren 7 Hari Mengukur Jarak, Bukan Jumlah Temuan
**Kategori:** UX / Dashboard Metric
**Urgensi:** Tinggi
**Dampak Bisnis:** Grafik utama di dashboard mengkomunikasikan "seberapa jauh perjalanan dilakukan" bukan "seberapa banyak temuan yang dikumpulkan". Ini adalah indikator performa yang salah untuk tujuan monitoring LILA.

**Rekomendasi:** Tambahkan ke ISSUES.md. Ubah sumbu Y dari Jarak (km) menjadi Jumlah Temuan per hari.

---

### ISS-005 — Opsi Filter & Sorting di Daftar Perjalanan Belum Optimal
**Kategori:** UX / Feature Gap
**Urgensi:** Rendah-Sedang
**Dampak Bisnis:** Operator tidak dapat dengan mudah menemukan "perjalanan dengan temuan terbanyak" atau "hanya perjalanan yang memiliki temuan". Sorting dan filtering yang tersedia masih dominan berbasis metrik tracking.

**Detail:**
- Tambahkan toggle filter: "Hanya tampilkan perjalanan dengan temuan"
- Pastikan opsi sort "Temuan terbanyak" dan "Foto terbanyak" tersedia dan berfungsi

**Rekomendasi:** Tambahkan ke ISSUES.md.

---

## 2. Identifikasi Duplikasi Issue

ISS-001 dan ISS-004 memiliki tema yang sama (dashboard tracking-centric). Keduanya tetap dicatat sebagai issue terpisah karena menyentuh komponen yang berbeda (summary cards vs grafik), tetapi dapat diselesaikan dalam satu iterasi yang sama.

ISS-001 dan ISS-002 juga berkaitan — keduanya tentang urutan prioritas visual. Dapat dijadikan satu iterasi: **"Reorientasi Visual Dashboard & Daftar Perjalanan ke Observation-Centric"**.

---

## 3. Ringkasan Rekomendasi

| Issue | Urgensi | Aksi |
|-------|---------|------|
| ISS-001 — Dashboard Tracking-Centric | Tinggi | Tambah ke ISSUES.md — kandidat iterasi berikutnya |
| ISS-002 — Summary Cards Bias Tracking | Sedang | Tambah ke ISSUES.md — bundel dengan ISS-001 |
| ISS-003 — Progress Bar Berbasis Track Point | Sedang | Tambah ke ISSUES.md — bundel dengan ISS-001 |
| ISS-004 — Grafik Tren Mengukur Jarak | Tinggi | Tambah ke ISSUES.md — bundel dengan ISS-001 |
| ISS-005 — Filter & Sorting Kurang Optimal | Rendah-Sedang | Tambah ke ISSUES.md — bundel dengan ISS-001 atau iterasi tersendiri |

**Rekomendasi Roadmap:** Semua issue berasal dari satu tema besar — **Reorientasi UI dari Tracking-Centric ke Observation-Centric**. Ini berkaitan langsung dengan visi produk di `AI_CONTEXT.md` dan sangat layak menjadi satu iterasi tersendiri, atau dimasukkan ke Iterasi 4 sebagai bagian dari penyegaran dashboard.
