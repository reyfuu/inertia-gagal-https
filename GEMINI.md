# TAMP APP — Inertia Edition (Panduan Instruksi Proyek)

Dokumen ini berisi arsitektur, konvensi kode, alur kerja, dan panduan teknis bersama untuk pengembangan **TAMP APP — Inertia Edition**. Dokumen ini wajib dipatuhi oleh seluruh pengembang (termasuk agen AI) demi menjaga kualitas, keamanan, dan konsistensi kode di seluruh repositori.

---

## 🛠️ Tech Stack & Ekosistem

| Layer | Teknologi | Detail & Batasan Versi |
| :--- | :--- | :--- |
| **Backend** | Laravel 13, PHP 8.3 | Fitur PHP modern (Attributes, Constructor Promotion, Strict Types) |
| **Frontend** | React 19, Inertia.js 2.0 | `@inertiajs/react ^3.2.0`, React Hooks, Functional Components |
| **Styling** | Tailwind CSS v4, Lucide React | Sistem transisi warna halus, Glassmorphism di Dark Mode |
| **Build Tool** | Vite 8 | Menangani kompilasi JS/JSX dan CSS |
| **Database** | SQLite / MySQL | Koneksi default development menggunakan SQLite (`database/database.sqlite`) |

---

## 🏗️ Arsitektur & Konvensi Penting (Mandatori)

### 1. Kompatibilitas WAF (Web Application Firewall)
Semua query database Eloquent **WAJIB** memanggil `Model::query()` secara eksplisit sebelum melakukan chaining metode lainnya. Hal ini untuk mencegah deteksi *false-positive* dari Web Application Firewall (seperti ModSecurity atau Cloudflare WAF) di lingkungan production.
*   **BENAR:** `User::query()->where('email', $email)->first();`
*   **SALAH:** `User::where('email', $email)->first();`

### 2. Tailwind CSS v4 & Dukungan Dark Mode
Proyek ini mengadopsi **Tailwind CSS v4** yang memiliki struktur konfigurasi berbeda dibandingkan v3.
*   **Sistem Dark Mode:** Tailwind v4 tidak lagi menggunakan properti `darkMode: 'class'` di file `tailwind.config.js` karena seluruh konfigurasi ditarik langsung ke CSS. Sebagai gantinya, file `resources/css/app.css` mendefinisikan varian kustom:
    ```css
    @custom-variant dark (&:where(.dark, .dark *));
    ```
*   **Theme Switcher:** Perpindahan tema dikelola oleh komponen `<ThemeSwitcher />` di frontend yang menyisipkan atau menghapus kelas `.dark` dari elemen `<html>` dan menyimpannya di `localStorage`.
*   **Pencegahan Transisi Instan (Flash):** Untuk menghindari kedipan warna yang salah (flash of incorrect theme) sebelum React di-mount, file `resources/js/app.jsx` menyertakan skrip inisialisasi tema instan di awal eksekusi. Jangan hapus atau modifikasi mekanisme ini tanpa alasan kuat.

### 3. Skema Desain Visual Premium
Aplikasi ini dirancang dengan gaya visual yang bersih, modern, dan sangat mendukung dark/light mode yang elegan (glassmorphism di dark mode, soft tints di light mode). Gunakan utilitas CSS kustom yang didefinisikan di `resources/css/app.css`:
*   **Container Card:** Gunakan `.card-premium` untuk membungkus konten utama halaman. Card ini otomatis menerapkan backdrop-filter blur 14px dan bayangan transparan saat dark mode aktif.
*   **Form Input:** Gunakan `.input-base` untuk menyamakan gaya form input (latar belakang bersih di light mode, soft glass di dark mode, serta fokus border berwarna indigo).
*   **Status Badges:** Untuk badge status (seperti persetujuan, revisi, atau peran), gunakan kelas kustom berikut:
    *   `.badge-emerald` : Status Disetujui / Active
    *   `.badge-rose` : Status Ditolak / Revisi / Suspended
    *   `.badge-amber` : Status Pending / Review
    *   `.badge-indigo` : Status Informasi Lainnya / General

### 4. Manajemen Peran (Roles) & Autentikasi
Aplikasi mendukung empat peran pengguna: **`super_admin`**, **`ka_prodi`**, **`dosen`**, dan **`mahasiswa`**.
*   **Penyaringan Data:** Controller harus menyaring data yang ditampilkan berdasarkan peran pengguna saat ini.
    *   `mahasiswa` hanya dapat melihat data bimbingan/laporannya sendiri.
    *   `dosen` hanya dapat melihat bimbingan/laporan mahasiswa yang ia bimbing (menggunakan `dosen_id` atau `dosen_pembimbing_id`).
    *   `super_admin` dan `ka_prodi` memiliki akses penuh untuk melihat dan mengelola seluruh data pengguna dan aktivitas akademik.
*   **Akses Menu Samping (Sidebar):** Sidebar menyembunyikan item navigasi tertentu menggunakan pencocokan role dari data auth yang dibagikan.

### 5. Pengiriman Data Inertia Shared Props
Data global dibagikan dari Laravel ke React secara otomatis melalui middleware `app/Http/Middleware/HandleInertiaRequests.php`.
*   **Auth Data (`auth.user`):** Berisi properti `id`, `name`, `email`, `role` (nama peran pertama), dan `roles` (daftar seluruh peran). Di React, akses data ini melalui `const { auth } = usePage().props;`.
*   **Sistem Notifikasi Flash (`flash`):** Menyediakan `flash.success` dan `flash.error`. Komponen `AuthenticatedLayout` mendengarkan perubahan pada properti flash ini untuk menampilkan Toast Notification secara otomatis. Gunakan redirect standard Laravel dengan `.with('success', 'Pesan Anda')` untuk memicu notifikasi ini.

---

## 💻 Alur Kerja & Perintah Terminal Utama

### 1. Proses Pemasangan Awal (Setup)
Untuk memulai proyek di lingkungan lokal dari awal:
```bash
# Menjalankan script setup otomatis (composer, env, key, migrate, npm install, build)
composer run setup
```
Atau langkah manualnya:
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

### 2. Menjalankan Server Pengembangan (Development)
Untuk menjalankan server Laravel (`php artisan serve`) sekaligus compiler aset Vite (`npm run dev`) secara bersamaan:
```bash
npm run dev:full
```
Server akan berjalan di port default dan dapat diakses melalui: **`http://localhost:5174`** (atau port lain yang dialokasikan oleh Vite).

### 3. Testing & Kualitas Kode
Pastikan semua perubahan kode lolos pengujian unit dan standar penulisan kode:
*   **Menjalankan Unit Test:**
    ```bash
    npm run test
    ```
    *(Menghapus config cache terlebih dahulu lalu menjalankan `php artisan test`)*
*   **Merapikan Format PHP (Linting):**
    ```bash
    ./vendor/bin/pint
    ```
    *(Menggunakan Laravel Pint untuk memformat ulang kode PHP agar sesuai standar PSR-12)*

---

## 📌 Pedoman Bagi Pengembang & Asisten AI

1.  **Jangan Merusak Transisi Mode Gelap:** Setiap kali menambahkan elemen UI baru (seperti tombol, form, modal, atau tabel), pastikan gaya tersebut mendukung `.dark` dengan kontras warna yang tinggi dan keterbacaan yang baik.
2.  **Gunakan Komponen Reusable:** Utamakan penggunaan komponen layout `<AuthenticatedLayout>` untuk membungkus halaman internal dan pastikan ikon yang digunakan bersumber dari library `lucide-react`.
3.  **Pertahankan Validasi yang Ketat:** Gunakan `Validator::make` atau Class Request (seperti `BimbinganRequest`, `LaporanRequest`) di Laravel untuk memvalidasi setiap payload dari frontend sebelum dimasukkan ke database.
4.  **Tinggalkan Log yang Bersih:** Bersihkan sisa-sisa debug (`console.log` di JavaScript atau `dd()`, `dump()` di PHP) sebelum melakukan commit kode ke repositori.
5.  **Perbarui Panduan Ini:** Jika terdapat perubahan struktur repositori yang signifikan, tambahkan informasi penting tersebut ke dalam dokumen `GEMINI.md` ini agar tim tetap sinkron.
