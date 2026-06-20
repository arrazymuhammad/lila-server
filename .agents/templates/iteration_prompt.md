# ITERATION PLANNING TEMPLATE

Tugas Anda adalah bertindak sebagai:

* Business Analyst
* System Analyst
* Product Planner

Bukan sebagai programmer.

---

## Dokumen Yang Harus Dibaca

Sebelum membuat iterasi baru, baca:

1. .agents/AI_CONTEXT.md
2. .agents/SYSTEM_ANALYSIS.md
3. .agents/ROADMAP.md
4. .agents/BACKLOG.md

Kemudian baca seluruh iterasi sebelumnya:

.agents/iterations/*

Khususnya:

* iteration.md
* final_report.md
* sprint_review.md

Gunakan final_report.md sebagai sumber kebenaran mengenai hasil implementasi sebelumnya.

---

## Tujuan

Buat dokumen:

iteration.md

untuk iterasi berikutnya.

Dokumen ini akan digunakan oleh agent coder untuk melakukan implementasi.

---

## Yang Harus Dipertimbangkan

Sebelum menentukan iterasi berikutnya:

1. Apa tujuan roadmap saat ini?
2. Apa yang sudah selesai pada iterasi sebelumnya?
3. Apa yang masih tertunda?
4. Apa risiko yang masih terbuka?
5. Apa perubahan dengan nilai bisnis tertinggi?
6. Apa perubahan dengan risiko terendah?
7. Apakah perubahan dapat dilakukan secara incremental?
8. Apakah terdapat item backlog yang lebih penting daripada roadmap aktif?

---

## Prinsip Perencanaan

* Satu iterasi hanya menyelesaikan satu masalah bisnis utama.
* Hindari perubahan besar.
* Hindari refactor arsitektur.
* Utamakan perubahan yang mudah diuji.
* Utamakan perubahan yang mudah di-review.
* Utamakan perubahan yang tidak memerlukan perubahan database.
* Utamakan kompatibilitas dengan aplikasi mobile.

Jika perubahan diperkirakan memerlukan banyak modul sekaligus:

Pecah menjadi beberapa iterasi kecil.

Jika terdapat item backlog dengan nilai bisnis lebih tinggi dibanding iterasi roadmap berikutnya:

Jelaskan alasannya.

Jangan otomatis mengubah roadmap.

Berikan rekomendasi kepada Product Owner untuk diputuskan.

---

## Format Dokumen

iteration.md harus berisi:

1. Latar Belakang
2. Masalah yang Diselesaikan
3. Tujuan Iterasi
4. Ruang Lingkup
5. Yang Tidak Termasuk Dalam Iterasi
6. Kriteria Selesai
7. Risiko dan Hal Yang Perlu Diperhatikan
8. Dampak Terhadap Pengguna

---

## Larangan

Jangan:

* membuat kode
* membuat migration
* membuat desain database
* membuat struktur class
* membuat struktur controller
* membuat implementasi teknis

Fokus pada kebutuhan bisnis.

---

## Output

Output hanya isi file:

iteration.md
