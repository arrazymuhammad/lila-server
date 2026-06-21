# QA Checklist
## Iterasi 03-B — Pengayaan Kategori Temuan oleh Operator

---

## Functional Test

[x] Masuk ke Mode Review salah satu temuan (`/verifications/findings/{session}`) dan pastikan terdapat field input **Kategori Baku (Operator)** yang dapat diisi.
[x] Pastikan field Kategori Baku menampilkan **Auto-suggest (Datalist)** yang menarik kategori dari database saat pengguna mulai mengetik.
[x] Isi field Kategori Baku dengan nilai baru (kategori yang belum pernah ada), klik **Approve**, dan pastikan nilai tersebut tersimpan di database (`activity_events.operator_category`).
[x] Isi field Kategori Baku dengan memilih salah satu saran dari auto-suggest, klik **Approve**, dan pastikan nilai tersimpan dengan benar.
[*BUG FIXED*] Biarkan field Kategori Baku **kosong**, klik **Approve**, dan pastikan sistem masih dapat menyimpan temuan tanpa kategori operator (field bersifat opsional).
[x] Klik **Reject** pada sebuah temuan dan pastikan proses penolakan tetap berjalan mulus tanpa gangguan dari field Kategori Baku.
[x] Pastikan nilai `operator_category` tersimpan di database (bukan hanya di session/memory) — verifikasi dengan me-refresh halaman atau membuka detail temuan ulang.
[*NEXT FEATURE*] Pastikan field Kategori Baku di Mode Review menampilkan nilai yang sudah pernah diisi sebelumnya jika temuan sudah pernah diproses (edit-mode).

## UI Test

[x] Pastikan field Kategori Baku muncul dengan label yang jelas dan berbeda dari judul/kategori asli mobile (agar tidak membingungkan operator).
[x] Pastikan tampilan **Judul Asli** (dari mobile) dan **Kategori Baku (Operator)** ditampilkan secara terpisah dan jelas di Mode Review.
[x] Pastikan dropdown/datalist auto-suggest tampil rapi dan tidak menutupi elemen lain di halaman.
[x] Pastikan di halaman **Detail Temuan Publik** (`/findings/{event}`) terdapat tampilan `operator_category` yang sudah diisi oleh operator.
[x] Pastikan `operator_category` **tidak muncul** di tampilan publik jika nilainya masih kosong (null) — tidak ada label kosong yang mengganggu.

## Visibility & Data Integrity Test

[x] Buka halaman `/findings/{event}` untuk temuan yang sudah diverifikasi dengan `operator_category` terisi — pastikan nilai kategori operator tampil di halaman detail publik.
[x] Pastikan `operator_category` yang tersimpan tidak menimpa atau mengubah nilai `title` (kategori asli dari mobile) — keduanya harus berdampingan secara independen.
[*TO BE TESTED*] Lakukan resync dari mobile (jika tersedia Postman) — pastikan `operator_category` yang sudah diisi operator **tidak** ter-overwrite oleh `SyncController`.

## Auto-suggest Quality Test

[x] Ketik sebagian nama kategori yang sudah pernah digunakan sebelumnya — pastikan saran auto-suggest menampilkan kategori tersebut.
[x] Verifikasi bahwa auto-suggest hanya menampilkan nilai unik (tidak ada duplikat dalam daftar saran).
[x] Pastikan auto-suggest responsif dan tidak menyebabkan lag atau error pada halaman.

## Regression Test

[x] Jalankan ulang alur verifikasi lengkap (Lobi Sesi → Mode Review → Approve → Auto-Next) dan pastikan penambahan field Kategori Baku tidak merusak alur yang sudah ada dari Iterasi 03-A.
[x] Buka halaman Daftar Temuan (`/findings`) dan pastikan kolom/data tampilan tidak berubah secara tak terduga akibat perubahan model.
[x] Buka halaman Dashboard dan pastikan tidak ada error query yang berhubungan dengan kolom `operator_category`.
[x] Buka halaman Peta (`/map`) dan pastikan marker temuan masih tampil dengan benar, toggle masih berfungsi.

## Acceptance Criteria

[x] Kolom `operator_category` tersedia di tabel `activity_events` (ditambahkan manual oleh admin database).
[x] Operator dapat mengisi Kategori Baku saat melakukan verifikasi temuan di Mode Review.
[x] Field Kategori Baku memiliki fitur auto-suggest yang menarik data kategori dari database secara dinamis.
[x] Nilai `operator_category` tersimpan secara permanen dan tidak hilang setelah refresh.
[x] Nilai `operator_category` ditampilkan di halaman Detail Temuan Publik (`/findings/{event}`).
[x] Nilai `title` asli dari mobile tidak terpengaruh oleh pengisian `operator_category`.
[*BUG FIXED*] Field Kategori Baku bersifat wajib
[x] Tidak ada perubahan pada API mobile atau kontrak sinkronisasi.

## Notes / Catatan untuk Tester

> ⚠️ **Risiko Duplikasi Kategori:** Karena input berupa teks bebas (dengan auto-suggest), ada kemungkinan operator mengetik kategori yang sama dengan ejaan berbeda (misal: "Sampah Plastik" vs "sampah plastik" vs "Sampah plastik"). Catat jika ditemukan inkonsistensi semacam ini — ini adalah input penting untuk perencanaan **manajemen kategori master** di iterasi berikutnya.

> belum ada edit mode dalam proses review temuan. bisa dilakukan approve atau reject, tapi tidak bisa di edit. atau jika memang memerlukan edit, silahkan jadikan catatan untuk iterasi berikutnya. berikan gambaran detail terkait prosedur edit mode

> pertimbangan untuk proses isian kategori nantinya berurutan alphabetic. 
 
