# AI_CONTEXT.md

# LILA Web Project Context

## Overview

LILA (Lihat & Lapor) adalah aplikasi mobile dan web yang digunakan untuk patroli, pelayaran, dan pengamatan lapangan.

Aplikasi mobile bertugas mengumpulkan data.

Aplikasi web bertugas menganalisis data yang telah dikumpulkan.

Target pengguna:

* Nelayan
* Pokmaswas
* Petugas Lapangan
* Operator Monitoring

---

## Business Vision

LILA bukan aplikasi tracking GPS biasa.

Tracking hanya merupakan sarana untuk mengumpulkan data lapangan.

Nilai utama sistem adalah:

* Temuan Pengamatan
* Bukti Foto
* Lokasi Temuan
* Riwayat Perjalanan

Aplikasi web akan dikembangkan secara bertahap dari:

Tracking-Centric

menjadi:

Observation-Centric

---

## Business Terminology

Gunakan istilah berikut dalam seluruh UI.

### Perjalanan

Perjalanan = Tracking Session

Digunakan untuk:

* Daftar Perjalanan
* Detail Perjalanan
* Statistik Perjalanan

### Temuan Pengamatan

Temuan Pengamatan = Event

Digunakan untuk:

* Daftar Temuan
* Detail Temuan
* Peta Temuan

---

## Existing Mobile Application

Aplikasi mobile sudah berjalan.

Aplikasi mobile menghasilkan data:

* Tracking Session
* Track Point
* Photo
* Event

Web harus tetap kompatibel dengan aplikasi mobile.

---

## Critical Rules

JANGAN melakukan perubahan berikut:

* Database schema
* Migration
* Seeder
* Model
* Relasi Model
* API Contract
* Struktur data aplikasi mobile

JANGAN:

* Rename tabel
* Rename kolom database
* Membuat migration baru
* Mengubah payload API
* Mengubah struktur JSON yang digunakan mobile

Kompatibilitas mobile adalah prioritas utama.

---

## Current Development Strategy

Prioritas pengembangan saat ini:

1. Mempermudah analisis hasil lapangan
2. Menampilkan seluruh perjalanan pada peta
3. Menampilkan temuan pengamatan secara lebih menonjol
4. Meningkatkan pengalaman pengguna web
5. Menjaga kompatibilitas dengan aplikasi mobile

---

## Repository Scope

Untuk fase pengembangan saat ini:

### Allowed To Read

* app/*
* routes/*
* resources/*

### Allowed To Modify

* app/Http/Controllers/*
* routes/*
* resources/*

### Read Only

* app/Models/*

### Forbidden

* database/*
* config/*
* tests/*
* vendor/*
* bootstrap/*
* storage/*
* public/*
* .env
* .env.example
* .env.docker
* composer.json
* composer.lock
* package.json
* package-lock.json
* vite.config.*
* phpunit.xml

Jangan melakukan perubahan pada area Forbidden.

Jika implementasi membutuhkan perubahan pada area tersebut:

* Hentikan implementasi
* Jelaskan alasannya
* Tunggu konfirmasi

---

## Development Process

Sebelum melakukan perubahan:

1. Audit project terlebih dahulu.
2. Jelaskan struktur yang ditemukan.
3. Jelaskan file yang akan diubah.
4. Jelaskan alasan perubahan.
5. Jelaskan risiko perubahan.

Jangan langsung mengimplementasikan perubahan besar.

---

## Code Quality Principles

Utamakan:

* Reuse komponen yang sudah ada
* Perubahan minimal
* Risiko minimal
* Konsistensi UI
* Konsistensi navigasi

Hindari:

* Refactor besar-besaran
* Penggantian arsitektur
* Perubahan yang tidak diminta
* Optimisasi prematur

---

## Success Criteria

Perubahan dianggap berhasil apabila:

* Fitur baru berjalan
* Database tidak berubah
* API mobile tidak berubah
* Struktur model tidak berubah
* User experience meningkat
* Risiko regresi minimal
