# Backlog Review

> Dibuat pada: 2026-06-21
> Konteks: Pasca Iterasi 01 (Verifikasi Perjalanan) & Iterasi 02 (Visibility Rule)

---

## Ringkasan Evaluasi

Backlog saat ini berisi **5 item aktif** yang mencerminkan kebutuhan nyata yang muncul dari proses pengembangan dua iterasi pertama. Semua item valid dan relevan terhadap visi bisnis LILA. Namun tidak semua item siap atau perlu dipromosikan ke roadmap aktif dalam waktu dekat.

Evaluasi ini dilakukan dengan mempertimbangkan:
- Roadmap aktif yang masih memiliki Iterasi 3–7 yang terencana.
- Batasan teknis: tidak ada perubahan database/schema/API mobile kecuali ada keputusan eksplisit.
- Nilai bisnis vs kompleksitas implementasi.

---

## Item Yang Naik Prioritas

### BL-001 — Rejected Reason Persistence
**Dari:** High → **Tetap High** (dipertahankan, namun tidak dipromosikan sekarang)

**Alasan:** Item ini memiliki nilai bisnis nyata — operator yang menolak perjalanan tidak dapat meninggalkan catatan yang permanen, sehingga komunikasi antara operator dan petugas lapangan terganggu. Nilainya akan semakin tinggi seiring meningkatnya volume data yang perlu diverifikasi.

**Catatan:** Implementasi kemungkinan memerlukan penambahan satu kolom database (`rejection_reason`). Ini bertentangan dengan batasan "no migration" yang berlaku saat ini. Perlu keputusan eksplisit dari pemilik produk sebelum bisa dipromosikan.

---

### BL-002 — SyncController Status Reset
**Dari:** High → **Tetap High** (perlu dipantau lebih dekat)

**Alasan:** Ini adalah **risiko sistemik aktif** — setiap resync dari mobile dapat membatalkan hasil verifikasi yang sudah dilakukan operator. Dengan semakin banyak data yang masuk ke siklus verifikasi (Iterasi 1 & 2), dampak dari bug ini semakin besar.

**Catatan:** Memerlukan koordinasi dengan tim aplikasi mobile. Tidak bisa diselesaikan hanya dari sisi web tanpa risiko memutus kompatibilitas. Perlu diangkat sebagai diskusi prioritas dengan stakeholder teknis.

---

## Item Yang Turun Prioritas

Tidak ada item yang diturunkan prioritasnya. Semua item saat ini memiliki justifikasi yang cukup untuk tetap di level prioritasnya masing-masing.

---

## Item Yang Dipromosikan ke Roadmap

**Tidak ada item yang dipromosikan ke roadmap aktif saat ini.**

Alasan:
- BL-001 dan BL-002 (High Priority) terganjal oleh keterbatasan teknis dan memerlukan keputusan di luar tim development web.
- BL-003 (Authentication) memerlukan perancangan yang lebih komprehensif — tidak bisa disisipkan sebagai iterasi kecil karena dampaknya menyentuh seluruh routing aplikasi.
- BL-004 (Audit Trail) adalah turunan dari BL-001 dan BL-003 — lebih baik ditangani setelah kedua prasyaratnya selesai.
- BL-005 (CSV Export) memiliki nilai bisnis yang valid namun tidak mendesak; roadmap masih fokus pada fitur analisis inti.

---

## Item Yang Ditolak

Tidak ada item yang ditolak. Semua item memiliki landasan bisnis yang valid.

---

## Rekomendasi Backlog Terbaru

### Status Setiap Item

| ID | Nama | Prioritas | Status | Rekomendasi |
|----|------|-----------|--------|-------------|
| BL-001 | Rejected Reason Persistence | High | Open | Tahan — butuh keputusan soal migration database |
| BL-002 | SyncController Status Reset | High | Open | Eskalasi ke stakeholder teknis — risiko aktif |
| BL-003 | Authentication & Authorization | Medium | Open | Tahan — perlu perencanaan menyeluruh |
| BL-004 | Verification Audit Trail | Medium | Open | Tahan — bergantung pada BL-001 dan BL-003 |
| BL-005 | CSV Export | Low | Open | Tahan — belum mendesak |

### Tindakan Yang Disarankan

1. **BL-002 perlu diskusi segera** — Meskipun tidak dipromosikan ke roadmap iterasi fitur, risiko reset status oleh SyncController perlu dikomunikasikan ke tim mobile agar ada solusi jangka pendek (misalnya: mobile tidak melakukan resync untuk session yang sudah dikirim, atau menambahkan pengecekan status sebelum override).

2. **Pertimbangkan "iteration pintu keluar" untuk BL-001** — Jika pemilik produk memutuskan bahwa penyimpanan alasan penolakan cukup penting, satu migration sederhana (tambah kolom `rejection_reason` di `tracking_sessions`) bisa menjadi justifikasi yang kuat untuk melonggarkan batasan "no migration" sekali saja.

3. **BL-003 perlu masuk roadmap sebelum data dibagikan lebih luas** — Jika aplikasi akan diakses oleh lebih banyak pengguna atau dibuka ke publik, authentication harus menjadi prioritas segera. Selama penggunaan masih internal dan terbatas, ini bisa ditunda.
