# QA Report: Iterasi 08 - Rich Finding Popup

## Ringkasan Eksekutif QA
Verifikasi QA dilakukan terhadap modifikasi di [`app/Http/Controllers/MapController.php`](app/Http/Controllers/MapController.php) dan [`resources/views/maps/index.blade.php`](resources/views/maps/index.blade.php). Seluruh kriteria penerimaan berhasil diverifikasi dan lulus pengujian.

---

## Status Verifikasi Checklist

### 1. Peta Utama (/map)
- **Data Payload findings**: **LULUS (PASS)**
  - Deskripsi, timestamp, dan photos array berhasil dimuat di payload PHP.
  - Gambar menggunakan helper `url()` Laravel sehingga memetakan berkas public secara langsung.
- **Render Marker & Popup**: **LULUS (PASS)**
  - Popup ter-render dengan rapi berkat modifikasi CSS di `@section('head')`.
  - Fallback untuk temuan tanpa foto berhasil menampilkan "Tidak ada foto".
  - Judul, badge kategori, dan status ditampilkan sesuai data.
  - Link detail mengarah ke halaman yang tepat.
- **Logika Carousel Foto**: **LULUS (PASS)**
  - Logika carousel menggunakan window helper `window.changePhoto` berjalan mulus.
  - Penulisan format JSON (`data-photos='...'`) menggunakan kutipan tunggal (single quote) pada atribut HTML menghindarkan parsing error HTML.

### 2. Uji Regresi (Regression Testing)
- **Heatmap & Filter**: **LULUS (PASS)**
  - Heatmap rute dan heatmap temuan tetap beroperasi seperti biasa.
  - Filter drop-down kategori temuan bekerja dengan lancar.

---

## Kategorisasi Temuan QA

| Tipe Temuan | Deskripsi Temuan | Status | Tindakan / Rekomendasi |
| :--- | :--- | :--- | :--- |
| **UX Observation** | Ukuran gambar carousel dibatasi h-32 agar popup tetap proporsional dan tidak memakan terlalu banyak ruang vertikal peta. | **CLOSED** | Keputusan desain yang baik untuk melestarikan real-estate peta. |
| **Issue** | Parsing JSON string di properti HTML `data-photos` sebelumnya crash karena karakter kutipan ganda (`"`). | **RESOLVED** | Telah diperbaiki dengan membungkus nilai atribut memakai kutipan tunggal (`'`). |

---

## Kesimpulan Akhir
Fitur **Rich Finding Popup** siap untuk dirilis ke tahap produksi. Seluruh kriteria kelayakan pengujian terpenuhi tanpa ada _regression issue_ yang terdeteksi.
