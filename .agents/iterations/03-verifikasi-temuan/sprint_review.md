# Sprint Review
## Iterasi 03 (03-A & 03-B) — Verifikasi & Pengayaan Temuan Pengamatan

> Tanggal: 2026-06-21
> Status Iterasi: **Completed With Notes**

---

## Ringkasan

Iterasi 03 menyelesaikan dua sub-iterasi dalam satu siklus pengembangan: **03-A (Verifikasi Temuan Inti)** dan **03-B (Pengayaan Kategori oleh Operator)**. Ini adalah iterasi terbesar dan paling kompleks sejauh ini — mencakup modifikasi `SyncController`, penambahan infrastruktur database (manual), dua controller baru, tiga view baru, dan pembaruan Visibility Rule di empat controller sekaligus.

Dari sisi bisnis, sistem LILA kini telah memiliki **siklus verifikasi dua tingkat yang lengkap**: operator memverifikasi perjalanan terlebih dahulu, kemudian memverifikasi setiap temuan satu per satu — dilengkapi dengan kemampuan pengayaan kategori yang membuat data lapangan lebih bermakna untuk analisis.

Sebuah inovasi UX yang tidak direncanakan namun sangat berdampak adalah pendekatan **Session-based Triage dengan Mode Review Flashcard**, yang secara psikologis jauh lebih efisien dibanding antrian global ribuan baris.

---

## Tujuan Yang Tercapai

### Iterasi 03-A — Verifikasi Temuan Inti
- [x] Lobi Sesi tersedia di `/verifications/findings` — menampilkan perjalanan yang memiliki antrian temuan `submitted`.
- [x] Mode Review Flashcard memungkinkan verifikasi satu per satu dengan Auto-Next tanpa reload halaman.
- [x] Setiap temuan menampilkan foto, peta koordinat, kategori, dan deskripsi secara lengkap.
- [x] Override Protection aktif — temuan `verified` tidak ter-reset oleh resync mobile.
- [x] `/findings` (publik) hanya menampilkan temuan `verified`.
- [x] Peta memiliki toggle Strict/All dengan state tersimpan di `localStorage`.
- [x] `SyncController` diperbaiki — events baru masuk sebagai `submitted`.
- [x] Sub-menu "Temuan" ditambahkan di sidebar Verifikasi.

### Iterasi 03-B — Pengayaan Kategori Temuan
- [x] Field Kategori Baku (Operator) tersedia di Mode Review.
- [x] Auto-suggest dinamis menarik data kategori yang sudah pernah digunakan dari database.
- [x] Nilai `operator_category` tersimpan permanen di database secara independen dari `title` asli mobile.
- [x] Kategori operator ditampilkan di Detail Temuan publik (`/findings/{event}`).
- [x] Field bersifat opsional — temuan bisa di-approve tanpa mengisi kategori.

---

## Tujuan Yang Belum Tercapai

- **Edit Mode untuk Temuan Verified:** Tidak ada cara untuk mengoreksi `operator_category` atau data lain setelah temuan berstatus `verified` tanpa harus reject terlebih dahulu. Ini adalah kebutuhan yang baru teridentifikasi saat QA.
- **Auto-suggest Alphabetic:** Daftar saran kategori belum diurutkan alphabetically — minor namun berpengaruh pada konsistensi pemilihan kategori.
- **Override Protection (Resync) Belum Diuji Penuh:** Tidak tersedia sarana Postman untuk simulasi resync mobile — logika kode sudah benar secara teori namun belum terverifikasi di lingkungan nyata.

---

## Pembelajaran

1. **Session-based Triage adalah pilihan UX yang tepat.** Antrian global ribuan temuan akan menjadi beban kognitif yang berat. Memecah per-perjalanan membuat pekerjaan verifikasi terasa lebih terkontrol dan terukur.

2. **Teks bebas tanpa manajemen master adalah utang teknis.** Field `operator_category` dengan input teks bebas membuka risiko duplikasi kategori sejak hari pertama. Manajemen kategori master bukan sekadar fitur tambahan — ini adalah kebutuhan integritas data yang harus segera ditangani.

3. **Kolom database yang ditambahkan manual perlu dicatat dengan sangat jelas.** Karena kolom `status` dan `operator_category` tidak melalui migration, tidak ada jejak di kode. Dokumen `infrastructure_requirements.md` adalah satu-satunya catatan formal. Pola ini harus dipertahankan dan diikuti.

4. **Deviasi implementasi yang baik harus didokumentasikan.** Penggunaan Session-Queue alih-alih Global-Queue, dan namespace `Verification\FindingController`, adalah keputusan teknis yang tidak ada di `iteration.md` awal tetapi sangat meningkatkan kualitas. Ini membuktikan pentingnya ruang deviasi positif dalam laporan implementasi.

---

## Risiko Yang Masih Terbuka

| # | Risiko | Tingkat | Urgensi |
|---|--------|---------|---------|
| 1 | Duplikasi kategori akibat teks bebas | **Tinggi** | Harus diselesaikan sebelum data dianalisis |
| 2 | Edit Mode temuan verified tidak ada | Sedang | Menjadi hambatan operasional jika ada kesalahan kategori |
| 3 | Override protection belum diuji Postman | Sedang | Perlu divalidasi saat ada akses ke perangkat mobile |
| 4 | Antrian awal sangat panjang | Sedang | Operasional — bukan bug, tapi beban kerja nyata |
| 5 | Tidak ada audit trail penolakan temuan | Rendah | Sesuai BL-001 — ditunda sampai keputusan database |
| 6 | Auto-suggest tidak alphabetic | Rendah | UX minor, mudah diperbaiki |

---

## Evaluasi Roadmap

```
✅ Iteration 1:   Verifikasi Perjalanan          [SELESAI]
✅ Iteration 2:   Visibility Rule                [SELESAI]
✅ Iteration 3-A: Verifikasi Temuan (Inti)       [SELESAI]
✅ Iteration 3-B: Pengayaan Kategori Temuan      [SELESAI]
🔴 URGENT:        Manajemen Kategori Master      [TIDAK ADA DI ROADMAP]
📋 Iteration 4:   Heatmap Perjalanan             [DIRENCANAKAN]
📋 Iteration 5:   Kategori Temuan                [DIRENCANAKAN — tumpang tindih dengan 3-B]
📋 Iteration 6:   Heatmap Temuan                 [DIRENCANAKAN]
📋 Iteration 7:   Pelaporan dan Statistik         [DIRENCANAKAN]
```

**Evaluasi kritis:**
- Iterasi 5 (Kategori Temuan) di roadmap awal sudah **sebagian** terselesaikan oleh 03-B (input operator_category). Namun bagian yang paling kritis — manajemen master kategori — justru belum ada di roadmap sama sekali.
- Iterasi 4 (Heatmap Perjalanan) masih relevan dan bisa dikerjakan kapan saja karena tidak bergantung pada kategori.
- **Heatmap Temuan (Iterasi 6)** sangat bergantung pada kualitas kategori — sebaiknya dikerjakan setelah manajemen kategori master selesai.

---

## Rekomendasi Iterasi Berikutnya

Berdasarkan risiko aktif dan evaluasi roadmap, direkomendasikan untuk menyisipkan **Iterasi 3-C atau Iterasi 3.5 — Manajemen Kategori Master** sebelum lanjut ke Iterasi 4 atau 5:

**Kandidat Pekerjaan:**
1. Buat tabel master kategori (dengan keputusan schema dari Product Owner).
2. Buat halaman CRUD kategori di panel admin/operator.
3. Ubah field `operator_category` dari teks bebas menjadi dropdown terikat ke tabel master.
4. Migrasi data kategori yang sudah ada ke format standar.

Alternatif: Jika manajemen kategori belum prioritas, lanjutkan ke **Iterasi 4 (Heatmap Perjalanan)** yang tidak bergantung pada kategori, sambil mengeskalasi risiko duplikasi kategori ke stakeholder.

---

## Keputusan

- **Revisi roadmap** — Evaluasi apakah Iterasi 5 (Kategori Temuan) perlu digabung/diganti dengan Iterasi Manajemen Kategori Master yang lebih konkret dan mendesak.
- **Tambah kebutuhan baru** — Edit Mode untuk temuan verified perlu masuk ke Backlog sebagai item medium priority.
- **Tambah kebutuhan baru** — Auto-suggest alphabetic perlu masuk ke Backlog sebagai improvement minor.
