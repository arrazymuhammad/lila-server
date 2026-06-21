# ISSUES.md
## Issue Register — LILA Web Application

> Dokumen ini adalah register resmi semua issue yang teridentifikasi dari feedback pengguna, QA, dan review internal.
> Issue dapat dipromosikan ke BACKLOG atau langsung ke Roadmap melalui proses review.

---

## STATUS LEGEND

| Status | Keterangan |
|--------|-----------|
| Open | Baru terdaftar, belum ada keputusan |
| Confirmed | Dikonfirmasi valid dan perlu ditangani |
| In Backlog | Sudah dipindahkan ke BACKLOG.md |
| In Roadmap | Sudah masuk iterasi aktif |
| Resolved | Sudah diselesaikan |
| Rejected | Tidak akan dikerjakan |
| Duplicate | Duplikasi dari issue lain |

---

## OPEN ISSUES

### ISS-001 — Dashboard Masih Tracking-Centric

**Status:** Confirmed
**Kategori:** UX / UI Prioritization
**Urgensi:** Tinggi
**Sumber:** Gemini UX Review (raw/001.md)
**Tanggal:** 2026-06-21

**Deskripsi:**
Metrik utama dashboard masih didominasi oleh indikator tracking (jarak tempuh, rata-rata jarak). Angka temuan dan foto diposisikan secara visual sebagai data sekunder. Widget "Perjalanan Terjauh" mempromosikan perjalanan 315 km dengan 0 temuan sebagai pencapaian.

**Dampak Bisnis:**
Operator membaca dashboard dan mendapatkan kesan bahwa LILA adalah aplikasi logistik/GPS biasa. Kontradiksi dengan visi Observation-Centric.

**Aksi yang Disarankan:** Masukkan ke BACKLOG sebagai kandidat iterasi Reorientasi UI.

---

### ISS-002 — Urutan Summary Cards di Daftar Perjalanan Bias ke Tracking

**Status:** Confirmed
**Kategori:** UX / UI Prioritization
**Urgensi:** Sedang
**Sumber:** Gemini UX Review (raw/001.md)
**Tanggal:** 2026-06-21

**Deskripsi:**
Urutan summary cards di `/activities`: Total Perjalanan → Total Jarak → Total Durasi → Temuan → Foto.
Secara psikologis, indikator tracking (jarak, durasi) tampak lebih penting dari temuan pengamatan.

**Dampak Bisnis:**
Memperlambat transisi mindset operator ke observation-centric.

**Aksi yang Disarankan:** Bundel dengan ISS-001 dalam satu iterasi Reorientasi UI.

---

### ISS-003 — Indikator Progress Bar Kartu Aktivitas Berbasis Track Point

**Status:** Confirmed
**Kategori:** UX / Visual Logic
**Urgensi:** Sedang
**Sumber:** Gemini UX Review (raw/001.md)
**Tanggal:** 2026-06-21

**Deskripsi:**
Bar biru pada kartu perjalanan di `/activities` dihitung berdasarkan jumlah track point GPS, bukan nilai pengamatan. Perjalanan 315 km tanpa temuan memiliki bar lebih panjang dari perjalanan 16 km dengan 3 temuan.

**Dampak Bisnis:**
Operator salah mengidentifikasi perjalanan mana yang bernilai tinggi untuk dianalisis.

**Aksi yang Disarankan:** Bundel dengan ISS-001. Ubah formula kalkulasi: berikan bobot pengali untuk temuan dan foto.

---

### ISS-004 — Grafik Tren 7 Hari Dashboard Mengukur Jarak, Bukan Temuan

**Status:** Confirmed
**Kategori:** UX / Dashboard Metric
**Urgensi:** Tinggi
**Sumber:** Gemini UX Review (raw/001.md)
**Tanggal:** 2026-06-21

**Deskripsi:**
Grafik utama dashboard menampilkan tren jarak tempuh (km) per hari selama 7 hari terakhir. Untuk sistem monitoring temuan lapangan, indikator yang relevan seharusnya adalah jumlah temuan per hari.

**Dampak Bisnis:**
Grafik utama mengkomunikasikan KPI yang salah — kinerja monitoring dinilai dari kilometer bukan dari temuan.

**Aksi yang Disarankan:** Bundel dengan ISS-001. Ubah sumbu Y dari Jarak (km) menjadi Jumlah Temuan.

---

### ISS-005 — Opsi Filter & Sorting Daftar Perjalanan Belum Optimal

**Status:** Confirmed
**Kategori:** UX / Feature Gap
**Urgensi:** Rendah-Sedang
**Sumber:** Gemini UX Review (raw/001.md)
**Tanggal:** 2026-06-21

**Deskripsi:**
Opsi sorting di daftar perjalanan masih dominan metrik tracking. Belum ada toggle "Hanya tampilkan perjalanan dengan temuan". Opsi sort "Temuan terbanyak" dan "Foto terbanyak" perlu dipastikan tersedia dan diprioritaskan.

**Dampak Bisnis:**
Operator kesulitan menemukan perjalanan yang paling relevan untuk dianalisis.

**Aksi yang Disarankan:** Dapat dijadikan bagian dari ISS-001 atau iterasi filter tersendiri.

---

## RESOLVED ISSUES

*(Tidak ada)*

---

## REJECTED ISSUES

*(Tidak ada)*
