# 🎓 SPMB Narima26 - SMK Wikrama 1 Garut

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**Sistem Penerimaan Murid Baru (SPMB) V4** adalah aplikasi berbasis web yang digunakan oleh SMK Wikrama 1 Garut untuk mengelola seluruh proses pendaftaran, seleksi, hingga daftar ulang calon siswa baru.

## ✨ Fitur Utama

- **👥 Multi-Role Management**: Memiliki hak akses khusus untuk *Admin, Bendahara, Pewawancara, Kepala Sekolah, dan Guru*.
- **📝 Pendaftaran Online**: Form registrasi terintegrasi untuk calon siswa baru.
- **🗣️ Seleksi Wawancara**: Modul penilaian wawancara komprehensif untuk calon siswa dan orang tua.
- **💳 Manajemen Keuangan**: Pengelolaan dan rekapitulasi pembayaran (Biaya Seleksi, DSP, SPP, Seragam).
- **📱 Integrasi WhatsApp**: Fitur *Click-to-Chat* dengan template otomatis untuk mempermudah komunikasi dengan siswa atau orang tua.
- **📊 Laporan & Export**: Rekapitulasi data dalam bentuk tabel interaktif dan export CSV (mendukung mode simpel 15 kolom dan mode lengkap 65+ kolom).
- **🎨 UI/UX Responsif**: Antarmuka modern yang responsif dengan fitur collapsible sidebar.

---

## 🛠️ Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** & **NPM**
- **MySQL** / MariaDB

---

## 🚀 Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi secara lokal:

1. **Clone Repository**
   ```bash
   git clone https://github.com/indamuliana/narima26.git
   cd narima26
   ```

2. **Install Dependencies (PHP & Node.js)**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Konfigurasi Environment**
   Duplikat file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database dan Seeding**
   Jalankan migrasi beserta data dummy awal.
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui `http://127.0.0.1:8000`.

---

## 🔐 Akun Testing (Seeder)

Anda dapat login menggunakan salah satu akun role berikut dengan password default **`password`** (atau sesuaikan dengan password seeder spesifik Anda, umumnya ditandai dengan `[role]123`):

| Role | Email Login | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@wikrama.sch.id` | `admin123` |
| **Bendahara** | `bendahara@wikrama.sch.id` | `bendahara123` |
| **Pewawancara** | `pewawancara@wikrama.sch.id` | `pewawancara123` |
| **Kepala Sekolah** | `kepsek@wikrama.sch.id` | `kepsek123` |
| **Guru** | `guru@wikrama.sch.id` | `guru123` |

---

## 👨‍💻 Kontribusi
Jika Anda menemukan *bug* atau ingin melakukan perbaikan, silakan buat *Pull Request* atau ajukan *Issue*.

## 📄 Lisensi
Sistem ini bersifat tertutup (Proprietary) dan khusus digunakan oleh lingkungan internal **SMK Wikrama 1 Garut**.
