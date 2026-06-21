# ITERATION_04.md

## Reorientasi UI — Observation-Centric

---

## Pertimbangan Backlog vs Roadmap

Evaluasi backlog sebelum memulai:

| Item Backlog | Prioritas | Lebih Mendesak dari Iterasi 4? |
|---|---|---|
| BL-001 (Rejected Reason) | High | ❌ Masih terganjal keputusan migration |
| BL-002 (SyncController Reset) | High | ❌ Butuh koordinasi tim mobile |
| BL-009 (Manajemen Kategori Master) | High | ❌ Belum ada keputusan schema tabel baru |
| BL-007 (Edit Mode Temuan) | Medium | ❌ Tidak lebih mendesak |

**Kesimpulan:** Tidak ada item backlog yang lebih siap dan lebih bernilai dari Iterasi 4. Lanjutkan roadmap aktif.

---

## 1. Latar Belakang

LILA sedang bertransisi dari **Tracking-Centric** menjadi **Observation-Centric**. Visi ini sudah tercermin di backend (Visibility Rule, proses verifikasi dua tingkat), namun **belum tercermin di UI**.

Audit kode pada `DashboardController` mengungkapkan:
- `activityTrend` menggunakan `distance` (jarak km) sebagai sumbu Y grafik utama — bukan temuan.
- `highlightSession` di-query berdasarkan `orderByDesc('distance')` → widget hero menampilkan perjalanan terjauh, bukan perjalanan paling kaya temuan.
- `maxTrendDistance` dihitung untuk normalisasi bar grafik berbasis jarak.

Audit kode pada `ActivityController` mengungkapkan:
- `summary` di-index sesuai urutan: `total_sessions`, `total_distance`, `total_duration`, `total_events`, `total_photos` — urutan ini mencerminkan prioritas tracking.
- Sorting sudah tersedia (`events`, `photos`) namun default masih `latest('start_time')`.
- Tidak ada filter "hanya perjalanan dengan temuan".

Kondisi ini menciptakan **kontradiksi visi**: sistem sudah memverifikasi temuan sebagai data utama, tetapi UI masih menampilkan jarak sebagai indikator keberhasilan.

---

## 2. Masalah yang Diselesaikan

| Issue | Lokasi | Masalah |
|-------|--------|---------|
| ISS-001 | Dashboard | Grafik tren mengukur Jarak — bukan Temuan |
| ISS-002 | Dashboard | Widget hero = Perjalanan Terjauh (bukan Terkaya Temuan) |
| ISS-003 | Dashboard + Activities | Summary cards menonjolkan jarak dan durasi |
| ISS-004 | Activities | Progress bar kartu berbasis track point count — bukan nilai temuan |
| ISS-005 | Activities | Tidak ada filter "hanya perjalanan dengan temuan" |

---

## 3. Tujuan Iterasi

1. Grafik tren 7 hari di Dashboard mengukur **jumlah temuan per hari**, bukan jarak.
2. Widget hero Dashboard menampilkan **perjalanan paling kaya temuan**, bukan terjauh.
3. Summary cards di Dashboard dan Daftar Perjalanan mengurutkan **Temuan → Foto** di posisi terdepan.
4. Formula progress bar kartu perjalanan memberikan **bobot lebih besar** untuk temuan dan foto.
5. Daftar Perjalanan memiliki toggle filter: **"Hanya perjalanan dengan temuan"**.
6. Opsi sorting **"Temuan terbanyak"** menjadi opsi yang prominan.

---

## 4. Ruang Lingkup

### 4.1 Dashboard — Grafik Tren 7 Hari

**Saat ini:** `activityTrend` mengisi key `distance` untuk grafik.
**Perubahan:** Tambah key `events_count` ke setiap item trend. Di view, ubah grafik agar sumbu Y menggunakan `events_count` bukan `distance`. Pertahankan `distance` di data untuk keperluan lain.

**Perubahan controller (`DashboardController`):**
- Query `recentSessions` perlu menyertakan `withCount(['events' => verified])` agar `events_count` tersedia.
- Isi `activityTrend` dengan `events_count` per hari.
- Ubah `maxTrendDistance` → `maxTrendEvents` untuk normalisasi bar grafik.

### 4.2 Dashboard — Widget Hero (Highlight Session)

**Saat ini:** `$highlightSession` di-query dengan `orderByDesc('distance')`.
**Perubahan:** Ubah menjadi `orderByDesc('events_count')` — tampilkan perjalanan dengan temuan terbanyak.

### 4.3 Dashboard — Urutan Summary Cards

**Saat ini (dari `$stats`):** `total_sessions`, `total_distance`, `total_duration`, `total_events`, `total_photos`
**Perubahan di view:** Reorder tampilan cards menjadi: **Temuan → Foto → Total Perjalanan → Total Jarak**.
(Tidak perlu ubah urutan di controller — cukup ubah urutan render di Blade view.)

### 4.4 Daftar Perjalanan — Urutan Summary Cards

**Saat ini:** `total_sessions`, `total_distance`, `total_duration`, `total_events`, `total_photos`
**Perubahan di view:** Reorder menjadi: **Temuan → Foto → Total Perjalanan → Total Jarak**.

### 4.5 Daftar Perjalanan — Formula Progress Bar

**Saat ini:** Panjang bar di kartu dihitung dari `track_points_count`.
**Perubahan:** Ubah formula menjadi berbasis nilai pengamatan:
```
nilai = (events_count × bobot_temuan) + (photos_count × bobot_foto) + (track_points_count × bobot_kecil)
```
Bobot yang disarankan: temuan ×10, foto ×5, track point ×1. Normalisasi terhadap nilai maksimum di halaman yang sama.

### 4.6 Daftar Perjalanan — Filter "Hanya dengan Temuan"

**Perubahan di `ActivityController`:** Tambah kondisi filter:
```php
if ($request->boolean('has_findings')) {
    $query->whereHas('events', fn($q) => $q->where('status', 'verified'));
}
```
**Perubahan di view:** Tambah checkbox/toggle "Hanya tampilkan perjalanan dengan temuan".

### 4.7 Daftar Perjalanan — Sorting Default & Promosi Opsi

**Saat ini:** Sorting default = `latest('start_time')`. Opsi "Temuan terbanyak" sudah ada (`events`) tapi tidak menonjol.
**Perubahan di view:** Tampilkan opsi "Temuan terbanyak" sebagai opsi pertama atau beri label yang lebih jelas. Default tetap "Terbaru" untuk tidak mengejutkan pengguna lama.

---

## 5. Yang Tidak Termasuk Dalam Iterasi Ini

- Perubahan pada halaman Peta (`/map`).
- Perubahan pada halaman Detail Perjalanan (`/activities/{session}`).
- Perubahan pada halaman Daftar Temuan (`/findings`).
- Perubahan pada proses verifikasi.
- Penambahan grafik atau visualisasi baru (selain perubahan yang sudah ada).
- Perubahan pada API mobile.

---

## 6. Kriteria Selesai

- [ ] Grafik tren 7 hari di Dashboard menggunakan jumlah temuan sebagai sumbu Y.
- [ ] Widget highlight/hero di Dashboard menampilkan perjalanan paling kaya temuan (bukan terjauh).
- [ ] Summary cards di Dashboard diurulkan: Temuan → Foto → Total Perjalanan → Total Jarak.
- [ ] Summary cards di Daftar Perjalanan diurutkan: Temuan → Foto → Total Perjalanan → Total Jarak.
- [ ] Progress bar kartu perjalanan di `/activities` dihitung berdasarkan bobot temuan dan foto.
- [ ] Tersedia filter "Hanya perjalanan dengan temuan" di Daftar Perjalanan.
- [ ] Tidak ada perubahan pada API mobile atau data sinkronisasi.

---

## 7. Risiko dan Hal yang Perlu Diperhatikan

### 7.1 Grafik Temuan Bisa Kosong di Awal
Karena semua temuan lama berstatus `submitted`, jumlah temuan `verified` per hari kemungkinan sangat sedikit atau nol di awal. Grafik akan tampak kosong. Ini adalah kondisi yang diharapkan — bukan bug. Pertimbangkan untuk menampilkan pesan informatif jika data kosong.

### 7.2 Perubahan Visual yang Cukup Terasa
Bagi pengguna yang sudah terbiasa dengan dashboard lama, perubahan urutan cards dan grafik akan terasa berbeda. Tidak ada risiko teknis, namun mungkin perlu komunikasi ke operator.

### 7.3 Formula Progress Bar Bisa Menyebabkan Bar Seragam
Jika semua perjalanan dalam satu halaman memiliki jumlah temuan yang sama (misalnya semua 0), bar akan seragam/kosong. Pertimbangkan fallback ke track point jika semua temuan = 0.

---

## 8. Dampak Terhadap Pengguna

### Operator Monitoring
Dashboard kini mengkomunikasikan informasi yang relevan dengan tugas monitoring: berapa banyak temuan dikumpulkan hari ini, perjalanan mana yang paling produktif. Ini mengurangi kebingungan dan mempercepat pengambilan keputusan.

### Pimpinan / Pengambil Kebijakan
Widget hero yang menampilkan "Perjalanan Terkaya Temuan" memberikan sorotan pada aktivitas lapangan yang paling bernilai — bukan sekadar yang paling jauh.

### Petugas Lapangan
Tidak ada perubahan pada aplikasi mobile.
