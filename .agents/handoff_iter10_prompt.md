# Handoff to Agent: Preparation for Iteration 10

Dokumen ini berisi konteks dan *prompt* (instruksi) yang disiapkan agar Anda (User) dapat dengan mudah melanjutkan pengerjaan proyek LILA WebGIS pada keesokan harinya dengan Agen AI yang baru.

---

## Cara Menggunakan Dokumen Ini Besok

Saat Anda membuka sesi percakapan baru dengan agen AI, cukup **salin dan tempel (copy-paste)** teks di dalam kotak blok (prompt) di bawah ini.

### 📋 COPY PROMPT DI BAWAH INI:

```text
Halo Agent! Kita akan mulai mengerjakan Iterasi 10 untuk proyek LILA WebGIS.

Tugas Utama Iterasi 10: "Otentikasi API Mobile (Sanctum)"
Tujuan kita adalah mengamankan endpoint `/api/sync` menggunakan token API (Bearer Token) agar hanya aplikasi mobile yang sah yang dapat mengirim data.

Sebelum merencanakan apa pun, wajib pelajari konteks proyek dengan membaca dokumen berikut secara berurutan:
1. `.agents\AI_CONTEXT.md` (PENTING: Pahami aturan 'Forbidden' dan kompatibilitas mobile)
2. `.agents\SYSTEM_ANALYSIS.md` (Pahami alur SyncController)
3. `.agents\ROADMAP.md` (Baca deskripsi Iteration 10)
4. `.agents\BACKLOG.md` (Untuk melihat riwayat pekerjaan sebelumnya)

Konteks Tambahan:
- Pada Iterasi 09, kita sudah mengamankan rute WEB (Dashboard) dengan middleware `auth` sesi biasa.
- Rute `/api/sync` dibiarkan terbuka (publik) agar aplikasi mobile lama tidak rusak secara tiba-tiba.
- Di Iterasi 10 ini, Anda harus membuat mekanisme login/register khusus API (menghasilkan token) dan mulai memproteksi `/api/sync`.
- Ingat aturan `AI_CONTEXT.md`: Jika instalasi Sanctum memerlukan eksekusi Migration (`database/*`), Anda tidak boleh melakukannya sendiri. Buatkan dokumen `infrastructure_requirement.md` agar saya yang menjalankan perintahnya secara manual di terminal.

Langkah pertama Anda: Lakukan audit awal, kemudian buatkan Implementation Plan (Artifact) untuk Iterasi 10 agar saya bisa mereviewnya. Jangan langsung mengedit kode!
```

---

## Mengapa Prompt Ini Penting?

1. **Memberikan Konteks Instan**: Agen baru tidak memiliki memori tentang apa yang kita kerjakan hari ini (Iterasi 09). Prompt ini merangkum *state* terakhir aplikasi.
2. **Menjaga Batasan Keamanan (Guardrails)**: Mengingatkan agen baru tentang aturan ketat `AI_CONTEXT.md` (terutama larangan mengubah *database/migration* tanpa persetujuan). Ini sangat krusial karena instalasi Laravel Sanctum pada Iterasi 10 pasti akan membutuhkan pembuatan tabel migrasi `personal_access_tokens`.
3. **Memaksa Mode Perencanaan (Planning Mode)**: Mencegah agen langsung merombak sistem API tanpa rencana teknis (*Implementation Plan*) yang jelas untuk Anda periksa terlebih dahulu.

Dokumen ini telah saya simpan sebagai `.agents\handoff_iter10_prompt.md`. Anda bisa langsung menutup sesi ini dan beristirahat dengan tenang!
