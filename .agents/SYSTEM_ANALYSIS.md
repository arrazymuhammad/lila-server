# SYSTEM_ANALYSIS.md

> Audit dilakukan pada: 2026-06-21
> Ruang lingkup: `app/`, `routes/`, `resources/`
> Berdasarkan kode yang ditemukan. Tidak ada asumsi yang tidak didukung kode.

---

## 1. Gambaran Umum Sistem

LILA adalah aplikasi web Laravel yang berfungsi sebagai **dashboard analisis data lapangan**. Data dihasilkan oleh aplikasi mobile (Android) lalu diunggah ke server web melalui mekanisme sinkronisasi berbasis file ZIP. Web tidak menghasilkan data sendiri — seluruh data berasal dari mobile.

**Stack teknologi yang teridentifikasi:**

- Framework: Laravel (PHP)
- Templating: Blade
- CSS: TailwindCSS v4 (via `@import 'tailwindcss'` di `app.css`)
- JS interaktif: Alpine.js (via CDN `//unpkg.com/alpinejs`)
- Peta: Leaflet.js (via CDN `https://unpkg.com/leaflet`)
- Asset build: Vite
- Database: Belum diperiksa (di luar ruang lingkup audit)

**Catatan teknis:** Layout `app.blade.php` memuat Tailwind via CDN (`https://cdn.tailwindcss.com`) sekaligus via Vite — ada duplikasi pemuatan Tailwind yang berpotensi konflik.

---

## 2. Struktur Direktori yang Diaudit

```
app/
├── Http/
│   └── Controllers/
│       ├── Controller.php          (base controller kosong)
│       ├── DashboardController.php
│       ├── ActivityController.php
│       ├── FindingController.php
│       ├── MapController.php
│       └── Api/
│           └── SyncController.php
└── Models/
    ├── TrackingSession.php
    ├── TrackPoint.php
    ├── ActivityEvent.php
    ├── ActivityPhoto.php
    └── User.php

routes/
├── web.php
├── api.php
└── console.php

resources/
├── css/
│   └── app.css
├── js/
│   └── app.js     (kosong)
└── views/
    ├── welcome.blade.php
    ├── dashboard.blade.php
    ├── activities/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── findings/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── maps/
    │   └── index.blade.php
    ├── layouts/
    │   ├── app.blade.php
    │   ├── landing.blade.php
    │   └── partials/
    │       ├── landing-navbar.blade.php
    │       └── landing-footer.blade.php
    └── components/
        └── status-badge.blade.php
```

---

## 3. Model Data

### 3.1 TrackingSession
- **Tabel:** `tracking_sessions`
- **Primary key:** UUID (string), tanpa `timestamps`
- **Kolom:** `id`, `title`, `start_time`, `end_time`, `distance`, `duration_seconds`, `status`
- **Cast:** `start_time` → datetime, `end_time` → datetime, `distance` → float, `duration_seconds` → integer
- **Relasi:**
  - `hasMany(TrackPoint, session_id)` → track_points
  - `hasMany(ActivityEvent, session_id)` → events
  - `hasMany(ActivityPhoto, session_id)` → photos
- **Nilai status yang diketahui:** `submitted`, `verified`, `rejected` (dari komponen `status-badge`)

### 3.2 TrackPoint
- **Tabel:** `track_points`
- **Primary key:** ID default (tidak UUID), tanpa `timestamps`
- **Kolom:** `session_id`, `latitude`, `longitude`, `timestamp`
- **Cast:** `latitude` → float, `longitude` → float, `timestamp` → datetime
- **Relasi:** `belongsTo(TrackingSession, session_id)`

### 3.3 ActivityEvent (= Temuan Pengamatan)
- **Tabel:** `activity_events`
- **Primary key:** UUID (string), tanpa `timestamps`
- **Kolom:** `id`, `session_id`, `title`, `description`, `latitude`, `longitude`, `timestamp`
- **Cast:** `latitude` → float, `longitude` → float, `timestamp` → datetime
- **Relasi:**
  - `belongsTo(TrackingSession, session_id)`
  - `hasMany(ActivityPhoto, event_id)`
- **Catatan:** Tidak ada kolom `category` atau `type` — ini dikonfirmasi oleh pesan di view `findings/index.blade.php` baris 75.

### 3.4 ActivityPhoto
- **Tabel:** `activity_photos`
- **Primary key:** UUID (string), tanpa `timestamps`
- **Kolom:** `id`, `session_id`, `event_id`, `file_path`, `thumbnail_path`, `filename`, `latitude`, `longitude`, `timestamp`, `selected`
- **Cast:** `latitude` → float, `longitude` → float, `timestamp` → datetime, `selected` → boolean
- **Relasi:**
  - `belongsTo(TrackingSession, session_id)`
  - `belongsTo(ActivityEvent, event_id)` — `event_id` bisa null (foto bisa milik session langsung, tanpa event)
- **Catatan penting:** `event_id` bersifat opsional. Foto bisa berelasi ke session saja tanpa terikat ke event tertentu.

### 3.5 User
- **Kolom:** `name`, `email`, `password`
- **Catatan:** Model User ada, namun **tidak ada autentikasi** yang diterapkan di `routes/web.php`. Seluruh halaman web dapat diakses tanpa login.

---

## 4. Routing

### 4.1 Web Routes (`routes/web.php`)

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/` | closure | Redirect ke `/dashboard` |
| GET | `/dashboard` | DashboardController | index |
| GET | `/activities` | ActivityController | index |
| GET | `/activities/{session}` | ActivityController | show |
| GET | `/findings` | FindingController | index |
| GET | `/findings/{event}` | FindingController | show |
| GET | `/map` | MapController | index |
| GET | `/maps` | MapController | index |

**Catatan:**
- Route `/map` dan `/maps` menuju controller yang sama — ada duplikasi.
- Tidak ada middleware `auth` pada route manapun.

### 4.2 API Routes (`routes/api.php`)

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/api/sync` | closure | Mengembalikan string `"hehhe"` (route debugging/placeholder) |
| POST | `/api/sync` | SyncController | activity |

**Catatan:** Tidak ada autentikasi API (tidak ada token/API key).

---

## 5. Modul dan Controller

### 5.1 DashboardController

**Method:** `index()`

**Query yang dilakukan:**
- `TrackingSession::count()` → total_sessions
- `TrackingSession::sum('distance')` → total_distance
- `TrackingSession::sum('duration_seconds')` → total_duration
- `ActivityEvent::count()` → total_events
- `ActivityPhoto::count()` → total_photos
- `ActivityPhoto::where('selected', true)->count()` → selected_photos
- `TrackPoint::count()` → total_track_points
- `TrackingSession::latest('start_time')->withCount(['events','photos'])->take(8)` → latestActivities
- `TrackingSession::select('status')->groupBy('status')` → statusSummary
- Kalkulasi tren 7 hari terakhir dari `TrackingSession` (computed di PHP, bukan query agregat per hari)
- `ActivityEvent::with('session')->latest('timestamp')->take(5)` → latestEvents
- `ActivityPhoto::with('session')->latest('timestamp')->take(6)` → latestPhotos
- `TrackingSession::withCount(['events','photos'])->orderByDesc('distance')->first()` → highlightSession

**Data yang dikirim ke view:** `stats`, `latestActivities`, `statusSummary`, `activityTrend`, `maxTrendDistance`, `latestEvents`, `latestPhotos`, `highlightSession`

### 5.2 ActivityController

**Method `index(Request $request)`:**
- Filter: `q` (pencarian judul), `status` (filter status, `__unknown` untuk NULL)
- Sortir: `distance`, `duration`, `events`, `photos`, default = `latest('start_time')`
- Paginate: 12 per halaman dengan `withQueryString()`
- withCount: `events`, `photos`, `trackPoints`
- Summary: query agregat terpisah (tidak dari collection yang sudah dipaginasi)
- Statuses: query grup status untuk dropdown filter

**Method `show(TrackingSession $session)` (route model binding):**
- Load relasi: `trackPoints` (ordered by timestamp), `photos` (latest), `events.photos` (latest)
- Summary: dihitung dari collection yang sudah di-load (tidak ada query tambahan)

### 5.3 FindingController

**Method `index(Request $request)`:**
- Filter: `q` (judul atau deskripsi), `date` (filter per tanggal), `session_id` (filter per perjalanan)
- Eager load: `session:id,title,start_time`, `photos`
- withCount: `photos`
- Paginate: 12 per halaman
- Sessions dropdown: hanya sessions yang `whereHas('events')`
- Summary: query terpisah (total_findings, with_photos, with_coordinates, journeys_with_findings)

**Method `show(ActivityEvent $event)` (route model binding):**
- Load: `session.trackPoints` (ordered by timestamp), `photos` (latest)

**Catatan:** View `findings/index.blade.php` memuat track points dari session di `show()`, namun track points tersebut tidak digunakan di view `findings/show.blade.php` — hanya peta titik temuan yang ditampilkan, bukan rute lengkap.

### 5.4 MapController

**Method `index(Request $request)`:**
- Parameter: `month` (default bulan ini), `year` (default tahun ini)
- Validasi: month 1-12, year 2000-2100
- Query: Sessions pada bulan/tahun terpilih, dengan `trackPoints` (ordered), `events`, dan `withCount`
- Data `$routes`: diproses di PHP menjadi array dengan `color` dari palette 8 warna (index % 8)
- Data `$years`: daftar tahun unik dari semua sessions
- Track points yang koordinatnya NULL difilter sebelum dikirim ke frontend

### 5.5 SyncController (API)

**Method `activity()` (POST /api/sync):**
1. Validasi: file harus ada, mimes zip
2. Ekstrak ZIP ke `storage/app/temp/{uuid7}/`
3. Baca `metadata.json` dari hasil ekstraksi
4. Proses berurutan: `importSession()` → `importTrackPoints()` → `importEvents()` → `importPhotos()`
5. Return JSON `{ message: "Sinkronisasi berhasil" }`

**Struktur metadata.json yang diharapkan:**
```json
{
  "session": { "id": "...", "title": "...", ... },
  "track_points": [ { "latitude": ..., "longitude": ..., "timestamp": "..." }, ... ],
  "events": [ { "id": "...", "session_id": "...", "title": "...", ... }, ... ],
  "photos": [
    {
      "id": "...",
      "sessionId": "...",
      "eventId": "...",
      "filename": "...",
      "latitude": ...,
      "longitude": ...,
      "timestamp": "...",
      "selected": true
    }
  ]
}
```

**Catatan penting tentang SyncController:**
- `importTrackPoints()` menggunakan `TrackPoint::create()` bukan `updateOrCreate()` — artinya **track points akan duplikat jika session yang sama dikirim ulang**.
- `importSession()` menggunakan `updateOrCreate` dengan field `status` selalu di-set ke `'submitted'` — artinya status session akan **direset ke submitted setiap kali sinkronisasi**.
- File foto disalin ke `public/activity-photos/{session_id}/`, field `thumbnail_path` tidak diisi (`ActivityPhoto` punya kolom ini tapi SyncController tidak mengisinya).
- Direktori temp hasil ekstraksi **tidak dibersihkan** setelah proses selesai.

---

## 6. Views

### 6.1 Layout

**`layouts/app.blade.php`** — Layout utama untuk halaman web app:
- Sidebar kiri tetap (fixed), lebar 264px (`w-64`), warna `slate-900`
- Navigasi sidebar: Dashboard, Daftar Perjalanan, Daftar Temuan Pengamatan, Semua Rute, Verifikasi (disabled/placeholder)
- Header atas dengan logo LILA WebGIS
- Konten di `@yield('content')`
- Support `@yield('head')` dan `@yield('body_class')`

**`layouts/landing.blade.php`** — Layout untuk halaman landing/welcome:
- Menggunakan navbar dan footer terpisah (partials)
- Tidak ada sidebar

### 6.2 Halaman

| View | Layout | Deskripsi |
|------|--------|-----------|
| `welcome.blade.php` | landing | Landing page publik (download APK, fitur, cara kerja) |
| `dashboard.blade.php` | app | Dashboard operasional dengan statistik, tren, dan highlight |
| `activities/index.blade.php` | app | Daftar perjalanan dengan filter, sort, paginate |
| `activities/show.blade.php` | app | Detail perjalanan: peta + sidebar temuan (Alpine.js + Leaflet) |
| `findings/index.blade.php` | app | Daftar temuan dengan filter, paginate |
| `findings/show.blade.php` | app | Detail temuan: peta titik + foto + info |
| `maps/index.blade.php` | app | Peta semua rute pada bulan/tahun terpilih (Alpine.js + Leaflet) |

### 6.3 Komponen

**`components/status-badge.blade.php`:**
- Menerima prop `$status`
- Menampilkan badge berwarna sesuai status: `submitted` (kuning), `verified` (hijau), `rejected` (merah), default (abu-abu)
- Digunakan di `dashboard.blade.php`, `activities/index.blade.php`, `activities/show.blade.php`

---

## 7. Alur Pengguna

### 7.1 Alur Data dari Mobile ke Web
```
Aplikasi Mobile
    → Kumpulkan data (GPS tracking, foto, events)
    → Kemas data dalam file ZIP (dengan metadata.json + folder photos/)
    → POST /api/sync (multipart/form-data, field: file)
        → SyncController::activity()
        → Simpan ke database: tracking_sessions, track_points, activity_events, activity_photos
        → Salin foto ke public/activity-photos/{session_id}/
```

### 7.2 Alur Penggunaan Web App

**Dashboard:**
```
/ → redirect → /dashboard
    Tampilkan ringkasan: total, rata-rata, tren 7 hari, status, highlight, foto terbaru, temuan terbaru
    Klik "Lihat Perjalanan" → /activities
```

**Melihat Perjalanan:**
```
/activities
    Filter: nama (q), status, urutan
    Klik kartu perjalanan → /activities/{session}

/activities/{session}
    Tampilkan peta dengan rute (polyline biru)
    Sidebar kanan: daftar temuan dengan nomor urut
    Klik temuan di sidebar → scroll peta ke lokasi, tampilkan detail (foto, waktu, koordinat, deskripsi)
    Klik "Buka Detail Temuan" di sidebar → /findings/{event}
    Klik "Temuan" (button di atas peta) → toggle sidebar
```

**Melihat Temuan:**
```
/findings
    Filter: kata kunci, tanggal, perjalanan
    Klik kartu temuan → /findings/{event}

/findings/{event}
    Tampilkan: peta satu titik, detail info, galeri foto
    Klik "Buka Detail Perjalanan" → /activities/{session}
```

**Peta Semua Rute:**
```
/map atau /maps
    Pilih bulan dan tahun → filter otomatis
    Tampilkan semua rute pada peta (polyline berbeda warna)
    Klik rute → popup nama, jarak, link detail
    Temuan ditampilkan sebagai circle marker
    Sidebar kanan: daftar perjalanan pada periode terpilih
```

---

## 8. Kekuatan Sistem

1. **Struktur MVC yang bersih** — Controller, model, dan view terpisah dengan baik.

2. **Route model binding** — `ActivityController::show(TrackingSession $session)` dan `FindingController::show(ActivityEvent $event)` memanfaatkan fitur Laravel secara idiomatis.

3. **Eager loading yang konsisten** — Semua controller menghindari N+1 query dengan `with()` dan `withCount()`.

4. **Komponen status-badge dapat digunakan ulang** — Satu komponen Blade untuk badge status yang digunakan di tiga halaman berbeda.

5. **Filter dan paginate** — ActivityController dan FindingController mendukung filter yang cukup lengkap dengan URL query string yang dipertahankan (`withQueryString()`).

6. **Integrasi peta yang baik** — Leaflet.js digunakan di tiga halaman (show perjalanan, show temuan, peta semua rute) dengan data langsung dari PHP via `@json()`.

7. **Palette warna rute otomatis** — MapController memiliki fungsi `routeColor()` dengan 8 warna berbeda untuk membedakan rute pada peta.

8. **Alpine.js untuk interaktivitas** — Detail perjalanan menggunakan Alpine.js untuk sidebar toggle, navigasi foto, dan interaksi peta tanpa perlu API tambahan.

9. **UUID pada entitas utama** — TrackingSession, ActivityEvent, ActivityPhoto menggunakan UUID — cocok untuk sinkronisasi dari banyak perangkat mobile tanpa konflik ID.

10. **Landing page terpisah** — Sistem memiliki dua layout (app & landing) yang terpisah dengan baik.

---

## 9. Kelemahan Sistem

### 9.1 Kritis

1. **Tidak ada autentikasi** — Seluruh halaman web dapat diakses tanpa login. Tidak ada middleware `auth` pada route manapun. Data lapangan tersedia secara publik.

2. **Track points tidak di-dedup saat sync** — `SyncController::importTrackPoints()` menggunakan `TrackPoint::create()`, sehingga jika session yang sama dikirim ulang, track points akan terduplikasi. Ini akan merusak visualisasi rute.

3. **Status session di-reset ke `submitted` setiap sync** — `importSession()` selalu menyertakan `'status' => 'submitted'` dalam `updateOrCreate()`, sehingga session yang sudah diverifikasi akan kembali ke `submitted` jika data dikirim ulang.

4. **Direktori temp tidak dibersihkan** — Setelah sinkronisasi, folder di `storage/app/temp/` tidak dihapus, berpotensi memenuhi disk.

### 9.2 Sedang

5. **Tidak ada autentikasi API** — Endpoint `POST /api/sync` dapat dipanggil oleh siapapun tanpa token.

6. **Route duplikat** — `/map` dan `/maps` menuju controller yang sama.

7. **Duplikasi Tailwind** — `layouts/app.blade.php` memuat Tailwind via CDN sekaligus via Vite — berpotensi konflik atau pemuatan ganda.

8. **Duplikasi helper `$formatDuration`** — Fungsi format durasi (jam/menit) didefinisikan ulang di tiga view berbeda (`dashboard.blade.php`, `activities/index.blade.php`, `activities/show.blade.php`) — tidak dibagikan melalui komponen atau helper.

9. **Duplikasi `$statusLabels`** — Array label status didefinisikan ulang di `dashboard.blade.php` dan `activities/index.blade.php`.

10. **`thumbnail_path` tidak diisi saat sync** — SyncController tidak mengisi `thumbnail_path` di `ActivityPhoto`, maka semua tampilan foto menggunakan `file_path` sebagai fallback.

11. **Track points dari session di-load di `FindingController::show()`** tapi tidak ditampilkan di view `findings/show.blade.php`. Ini adalah query yang tidak terpakai.

### 9.3 Minor

12. **`app.js` kosong** — File `resources/js/app.js` hanya berisi 3 byte (kemungkinan kosong atau whitespace). Semua JS ditulis inline di view.

13. **`Controller.php` kosong** — Base controller tidak memiliki method atau middleware apapun.

14. **Route GET `/api/sync` mengembalikan string debugging** (`"hehhe"`) — Bukan JSON response yang proper.

15. **Tidak ada error handling di SyncController** — Jika `metadata.json` memiliki struktur yang tidak terduga, proses akan gagal dengan error PHP tanpa pesan yang informatif bagi mobile.

16. **Landing page tidak merespons state data** — Halaman `welcome.blade.php` tidak memuat data apapun dari database (tidak ada controller). Tidak ada dinamisme dari backend.

17. **Fitur "Verifikasi" belum diimplementasikan** — Tombol di sidebar diberi label "Verifikasi (Soon)" dan dinonaktifkan (`opacity-50 cursor-not-allowed`).

---

## 10. Area yang Perlu Dipahami Lebih Lanjut

1. **Database schema** — Tabel belum diperiksa (di luar ruang lingkup). Perlu dipahami: apakah ada index pada `session_id`, `timestamp`, dan kolom koordinat untuk performa query peta dengan banyak track points.

2. **Format payload mobile yang sesungguhnya** — Nama field di `importPhotos()` menggunakan camelCase (`sessionId`, `eventId`) berbeda dari snake_case di model. Perlu dikonfirmasi format ZIP yang dikirim mobile untuk memastikan pemetaan sudah benar.

3. **Cara `thumbnail_path` seharusnya diisi** — Apakah thumbnail dibuat di mobile (sudah ada di ZIP) atau dibuat di server?

4. **Apakah `event_id` di ActivityPhoto selalu ada** — Dari model, `event_id` bisa null. Dari SyncController, field diisi dari `photo['eventId']`. Perlu dipahami kapan foto tidak berelasi ke event.

5. **Skala data yang diharapkan** — Berapa banyak track points per session? Berapa session yang ada? Ini berdampak pada performa `MapController` yang memuat semua track points dalam satu bulan sekaligus.

6. **Proses verifikasi yang direncanakan** — Fitur "Verifikasi" disebut di landing page dan ada sebagai status (`verified`, `rejected`), namun belum ada halaman atau controller untuk alur verifikasi.

7. **Aplikasi mobile** — Kode mobile tidak diaudit. Pemahaman tentang kapan dan bagaimana sinkronisasi dilakukan (manual vs otomatis, frekuensi, dll.) akan membantu menilai risiko masalah duplikasi track points.

---

*Dokumen ini dibuat berdasarkan pembacaan langsung kode di `app/`, `routes/`, dan `resources/`. Tidak ada kode yang diubah selama proses audit.*
