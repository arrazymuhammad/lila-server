# Kebutuhan Infrastruktur & Database (Iterasi 03-B)

Dokumen ini berisi instruksi dan pencatatan penyesuaian level database/skema yang harus dilakukan oleh *Product Owner* atau *Database Administrator*, dikarenakan aturan agen untuk tidak melakukan eksekusi modifikasi *database migrations* secara otomatis.

---

## 1. Konfirmasi Kolom Kategori (Sudah Selesai)
Telah dikonfirmasi bahwa tabel `activity_events` di database telah memiliki kolom `operator_category` (tipe data teks/string). Kolom ini digunakan mulai Iterasi 03-B sebagai tempat penyimpanan "Kategori Baku" yang diinput oleh operator selama proses verifikasi.

## 2. Pembaruan Model (Sangat Disarankan)
Untuk memastikan standar Laravel Eloquent berjalan mulus, khususnya pada fitur *Mass Assignment*, dimohon untuk menambahkan `operator_category` ke dalam atribut `$fillable` di file `app/Models/ActivityEvent.php`. 

```php
// app/Models/ActivityEvent.php
#[Fillable(['id', 'session_id', 'title', 'description', 'latitude', 'longitude', 'timestamp', 'status', 'operator_category'])]
class ActivityEvent extends Model
```
*(Catatan: Karena keterbatasan hak akses modifikasi, saat ini controller menggunakan bypass penyimpanan via penugasan properti eksplisit `$event->operator_category = ...`).*

## 3. Fitur Masa Depan: Manajemen Kategori (Perhatian Iterasi Berikutnya)
Saat ini kategori didasarkan pada pengetikan bebas dari tabel `activity_events` (menggunakan fitur Auto-suggest). Sebaiknya dipersiapkan untuk Iterasi 04/05:
- Pembuatan tabel master kategori (misalnya: `finding_categories`).
- Pembuatan antarmuka CMS untuk fungsi **Create, Read, Update, Delete** (CRUD) Kategori.
- Penyesuaian kolom di `activity_events` agar menggunakan *foreign key* dari tabel kategori master.
