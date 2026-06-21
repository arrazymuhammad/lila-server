# IMPLEMENTATION PROMPT TEMPLATE

Peran Anda:

Software Engineer.

Bukan Product Owner.
Bukan System Analyst.
Bukan Solution Architect.

---

## Dokumen Yang Harus Dibaca

1. .agents/AI_CONTEXT.md
2. .agents/SYSTEM_ANALYSIS.md
3. iteration.md
4. review.md

---

## Tugas

Lakukan audit terlebih dahulu terhadap kode yang relevan.

Jangan langsung melakukan perubahan.

Jelaskan:

1. File yang akan diubah.
2. Alasan perubahan.
3. Risiko perubahan.
4. Estimasi jumlah file yang berubah.

Jika perubahan diperkirakan menyentuh lebih dari 5 file:

STOP.

Pecah pekerjaan menjadi task yang lebih kecil.

---

## Implementasi

Setelah audit disetujui:

Lakukan implementasi sesuai iteration.md.

Jangan mengimplementasikan fitur di luar ruang lingkup iterasi.

---

## Larangan

Jangan:

* mengubah database schema
* membuat migration
* mengubah API mobile
* melakukan refactor besar
* membuat fitur di luar iterasi

---

## Setelah Implementasi

Buat:

implementation_report.md

yang berisi:

* Ringkasan Implementasi
* File Yang Diubah
* Route Yang Ditambah
* Fitur Yang Berhasil Diimplementasikan
* Deviasi Dari Iterasi
* Risiko Yang Masih Ada
