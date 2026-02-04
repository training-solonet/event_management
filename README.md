# Event-Management

## Deskripsi Project

Event Management adalah sebuah paltform yang dirancang untuk memudahkan baik user maupun admin dalam melakuka pendaftaran serta pengelolaan event, platform ini memberikan kemudahan akses dengan tampilan yang terbilang baik dan user frinedly, selain itu platform ini didesain sedemikian rupa untuk memudahkan pengelolaan event baik untuk skala kecil, menengah atau besar.

## ERD Diagram

Berikut adalah Entity Relationship Diagram (ERD) dari database POS Event-Management yang menunjukkan struktur dan relasi antar tabel:

![ERD Diagram Event Management](docs/erd.png)

# Event Management Database Documentation

Dokumentasi ini berisi penjelasan mengenai struktur database yang digunakan dalam sistem manajemen?pengelolaan event dan pendaftaran peserta.

## Database Schema (ERD)

Sistem menggunakan database relasional untuk mengelola pendaftaran, transaksi, dan operasional sistem secara otomatis.



## 📂 Penjelasan Tabel

### 1. Modul Utama (Core)
* **`events`**: Menyimpan detail acara.
    * `event_code`: Kode unik identitas event.
    * `available_slots` & `registered_count`: Untuk kontrol kuota peserta.
* **`participants`**: Data pendaftar yang terhubung ke event.
    * Menyimpan info personal (nama, email, phone, nik, gender).
    * Tracking pembayaran (`payment_status`, `payment_proof`).
    * Log notifikasi (`wa_notification_sent`, `email_notification_sent`).

### 2. Autentikasi & Pengguna
* **`users`**: Data admin/staf pengelola. Mendukung fitur keamanan seperti *Two-Factor Authentication* (2FA) dan role.
* **`personal_access_tokens`**: Token untuk akses API (Laravel Sanctum).
* **`password_reset_tokens`**: Penyimpanan token sementara untuk reset kata sandi.

### 3. Infrastruktur & Pembayaran
* **`payment_methods`**: Master data metode pembayaran (Nama bank, nomor rekening, status aktif).
* **`sessions`**: Menyimpan data sesi login user untuk menjaga persistensi akun.
* **`cache` & `cache_locks`**: Digunakan untuk mengoptimalkan kecepatan akses data dan penguncian proses sistem.

### 4. Queue & Log System (Antrean Kerja)
* **`jobs` & `job_batches`**: Mengelola proses background seperti pengiriman email massal.
* **`failed_jobs`**: Mencatat proses antrean yang gagal untuk keperluan debugging.
* **`migrations`**: Rekam jejak perubahan struktur database (version control database).


##  Relasi Utama (Key Relationships)

| Hubungan | Tabel Asal | Tabel Tujuan | Kunci (Foreign Key) |
| :--- | :--- | :--- | :--- |
| **One to Many** | `events` | `participants` | `event_id` |
| **One to Many** | `users` | `sessions` | `user_id` |
| **Polymorphic** | `users`/`others` | `personal_access_tokens` | `tokenable_id` |


##  Atribut Penting (Business Logic)
1.  **Pendaftaran**: Setiap peserta yang mendaftar ke tabel `participants` harus menyertakan `transaction_code` unik untuk verifikasi manual maupun otomatis.
2.  **Keamanan**: Tabel `users` memiliki kolom `two_factor_secret`, menunjukkan sistem ini mengutamakan keamanan akun pengelola.
3.  **Manajemen Kuota**: Kolom `available_slots` pada tabel `events` menjadi acuan validasi sebelum `participants` baru dapat ditambahkan.


## Environment
* **Framework Context**: Laravel 12.
* **Engine**: MySQL / MariaDB / PostgreSQL.

### Keunggulan Platform

Event management memiliki fitur yang simple dan mudah di gunakan bahkan untuk pemula sekalipun selain itu platform ini juga memberikan visualisasi yang baik untuk setiap fiturnya, alih alih hanya mementingkan fitur dan fungsional, kami menggabungkan dua aspek penting dalam website yaitu visual dan fungsional dalam satu platform yaitu Event management.

### Role dan Apa saja yang bisa di kerjakan

- **Peserta**: Peserta adalah role paling umum yang akan di dapatakan ketika pertamakali masuk webiste ini, selain itu peserta juga diberikan beberapa fitur penunjang untuk mempermudah penggunaan website seperti search, daftar dan sebagainya.

Path: "/"

Kendali peserta meliputi: Melakukan pendaftaran event, melakukan search event, melakukan pencarian data pendaftaran berdasarkan kode transaksi.

- **Admin**: Admin adalah role khusus yang memiliki wewenang khusus pula untuk melakukan pengelolaan event, selain itu admin juga memiliki halaman khusus yang memungkinkan admin melakuan pengelolaan event, peserta ataupun metode pembayaran. 

Path: "/admin"

Kendali Admin meliputi: Melakukan monitoring event, melakukan monitoring peserta dari tiap tiap event, melakukan penambahan atau penghapusan event, melakukan verifikasi peserta, mengirim email konfirmasi berisi kode QR, menambahkan, mengaktifkan/ menonaktifkan metode pembayaran.

### Fitur Utama Peserta

- **Pendaftaran event**: Memungkinkan peserta melakukan pendaftaran event yang sudah di sediakan
- **Pencarian berdasarkan kode transaksi**: memungkinkan peserta melakukan pencarian berdasarkan kode transaksi
- **Redirect Whatsapp**: Memungkinkan pendaftar mengirimkan informasi pendafatrannya kepada pihak penyelenggara

### Fitur Utama Admin

- **Monitoring Dashboard**: Memungkikan admin melakukan melakukan monitoring keseluruhan untuk event dan juga peserta.
- **Management Event**: Memungkinkan admin melakukan pengelolaan seperti penambahan, pengeditan serta memonitoring peserta yang terdaftar di event ternetu.
- **Management Peserta**: Memungkinkan admin melakukan pengelolan peserta yang sudah melakukan pendaftaran seperti melakukan verifikasi pembayaran, mengirimkan email konfirmasi serta melihat informasi pendafatran peserta.
- **Fitur Mentode Pembayaran**: Memungkinkan admin melakukan pengelolaan metode pembayaran seperti menambahkan, menghapus atau menonaktifkan metode pembayaran.
- **Kirim Email Konfirmasi**: Memungkinkan admin mengirimkan pesan email konfirmasi yang berisi informasi pendaftar serta kode QR untuk memverifikasi peserta.
- **Export Data**: Export laporan dalam berbagai format (Excel, PDF)

### Teknologi yang Digunakan

- **Backend**: Laravel 12
- **Frontend**: Livewire, Tailwind CSS, Vite, Axios, Font Awesome
- **Database**: MySQL
- **Export**: Maatwebsite Excel
- **QR Code Generator**: Js QrCode
- **Authentication**: Laravel Fortify & Jetstream

## Instalasi

### Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Langkah Instalasi

1. Clone repository
```bash
git clone https://github.com/training-solonet/pos-sanjaya.git
cd pos-sanjaya
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database di file `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_sanjaya
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migrasi database
```bash
php artisan migrate
```

6. Build assets
```bash
npm run build
# atau untuk development
npm run dev
```

7. Jalankan aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Development

### Running Development Server
```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Vite
npm run dev
```

### Code Quality
```bash
# Fix PHP code style
./vendor/bin/pint

