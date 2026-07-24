# 📚 Web Edukasi - Platform Pembelajaran Interaktif

Sistem manajemen pembelajaran untuk Sekolah Dasar dengan fitur pembuatan soal oleh guru dan verifikasi admin.

## 🎯 Fitur Utama

### 1. **Authentication & Authorization**
- Login dan Registrasi User
- Role-based access control (Admin & Guru)
- Sistem verifikasi guru oleh admin
- Status verification (pending, verified, rejected)

### 2. **Admin Dashboard**
- Melihat daftar guru yang menunggu verifikasi
- Melihat daftar guru yang sudah terverifikasi
- Verifikasi atau menolak guru baru

### 3. **Guru Dashboard**
- Membuat soal dengan detail lengkap:
  - Pilih kelas (Kelas 1 SD, Kelas 2 SD, dll)
  - Pilih tingkat kesulitan (Mudah, Normal, Sulit)
  - Input pertanyaan dan 4 pilihan jawaban
  - Tentukan jawaban yang benar
- Melihat daftar soal yang telah dibuat
- Edit dan hapus soal

## 🗄️ Database Schema

### Users Table
```
- id (Primary Key)
- name
- email (Unique)
- password (Hashed)
- role (admin/guru)
- status (pending/verified/rejected)
- timestamps
```

### Kelas Table
```
- id (Primary Key)
- nama_kelas
- timestamps
```

### Kategori Table
```
- id (Primary Key)
- nama_kategori (enum: mudah, normal, sulit)
- timestamps
```

### Soal Table
```
- id (Primary Key)
- user_id (FK → users)
- kelas_id (FK → kelas)
- kategori_id (FK → kategori)
- pertanyaan
- pilihan_a, pilihan_b, pilihan_c, pilihan_d
- jawaban_benar (enum: a, b, c, d)
- timestamps
```

## 📝 Account Default untuk Testing

Sistem sudah dilengkapi dengan data default untuk testing:

### Admin Account
- Email: `admin@edukasi.local`
- Password: `password123`
- Role: Admin

### Guru Account (Pending Verification)
- Email: `guru@edukasi.local`
- Password: `password123`
- Role: Guru
- Status: Pending (butuh verifikasi dari admin)

### Available Classes
- Kelas 1 SD
- Kelas 2 SD

### Available Categories
- Mudah
- Normal
- Sulit

## 🚀 Cara Menggunakan

### Step 1: Setup Awal
```bash
# Jalankan migrations dan seeder
php artisan migrate:fresh --seed

# Atau jika sudah pernah, cukup jalankan seeder
php artisan db:seed
```

### Step 2: Login sebagai Admin
1. Buka browser dan akses aplikasi
2. Klik "Login"
3. Masukkan email: `admin@edukasi.local` dan password: `password123`
4. Klik "Login"

### Step 3: Verifikasi Guru
1. Di dashboard admin, lihat list "Guru Menunggu Verifikasi"
2. Ada guru dengan nama "Guru Test" dengan status pending
3. Klik tombol "Verifikasi" untuk menyetujui atau "Tolak" untuk menolak

### Step 4: Login sebagai Guru
1. Logout dari admin account
2. Klik "Register" atau langsung ke `/register`
3. Atau login dengan account guru:
   - Email: `guru@edukasi.local`
   - Password: `password123`
4. Jika sudah verified oleh admin, bisa akses `/guru/dashboard`

### Step 5: Membuat Soal
1. Di guru dashboard, klik "Tambah Soal"
2. Pilih kelas dan tingkat kesulitan
3. Isi pertanyaan dan 4 pilihan jawaban
4. Pilih jawaban yang benar
5. Klik "Simpan Soal"

### Step 6: Manage Soal
1. Klik menu "Soal Saya" atau akses `/guru/soal`
2. Lihat daftar semua soal yang telah dibuat
3. Bisa Edit atau Hapus soal

## 🔐 Middleware & Security

### AdminMiddleware
Mengecek:
- User sudah login (auth)
- Role adalah admin
- Status sudah verified

### GuruMiddleware
Mengecek:
- User sudah login (auth)
- Role adalah guru
- Status sudah verified

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Login, Register, Logout
│   │   ├── AdminController.php     # Admin dashboard & verification
│   │   └── GuruController.php      # Guru dashboard & soal management
│   ├── Middleware/
│   │   ├── AdminMiddleware.php     # Admin role checker
│   │   └── GuruMiddleware.php      # Guru role checker
│   └── ...
├── Models/
│   ├── User.php
│   ├── Kelas.php
│   ├── Kategori.php
│   └── Soal.php
└── ...

resources/views/
├── layouts/
│   └── app.blade.php               # Base layout with navbar
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── admin/
│   └── dashboard.blade.php
├── guru/
│   ├── dashboard.blade.php
│   └── soal/
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── list.blade.php
└── welcome.blade.php

routes/
└── web.php                         # Semua routes

database/
├── migrations/
│   ├── *_create_users_table.php
│   ├── *_create_kelas_table.php
│   ├── *_create_kategori_table.php
│   └── *_create_soal_table.php
└── seeders/
    ├── AdminSeeder.php
    ├── KelasSeeder.php
    ├── KategoriSeeder.php
    └── DatabaseSeeder.php
```

## 🔄 Workflow

### Alur Guru Baru

1. **Registrasi**
   - Guru mengisi form registrasi
   - Account dibuat dengan status `pending`

2. **Menunggu Verifikasi**
   - Admin melihat guru baru di dashboard
   - Admin memilih untuk verify atau reject

3. **Setelah Diverifikasi**
   - Guru bisa login
   - Guru bisa akses dashboard dan membuat soal

### Alur Membuat Soal

1. Guru login
2. Klik "Tambah Soal" di dashboard
3. Pilih kelas dan kategori kesulitan
4. Isi data soal lengkap
5. Submit untuk disimpan ke database
6. Soal tampil di "Soal Saya"

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Database**: MySQL
- **Frontend**: Blade Template + Tailwind CSS
- **Authentication**: Laravel Built-in Auth

## 📱 Responsive Design

Semua halaman sudah responsive dan dapat diakses dari:
- Desktop
- Tablet
- Mobile phone

## 🔗 URL Routes

```
Public Routes:
GET  /                          # Home page
GET  /login                     # Login form
POST /login                     # Login process
GET  /register                  # Register form
POST /register                  # Register process
POST /logout                    # Logout

Admin Routes (require auth + admin role):
GET  /admin/dashboard           # Admin dashboard
POST /admin/guru/{user}/verify  # Verify guru
POST /admin/guru/{user}/reject  # Reject guru

Guru Routes (require auth + guru role + verified status):
GET  /guru/dashboard            # Guru dashboard
GET  /guru/soal                 # List soal
GET  /guru/soal/create          # Create soal form
POST /guru/soal                 # Store soal
GET  /guru/soal/{soal}/edit     # Edit soal form
PUT  /guru/soal/{soal}          # Update soal
DELETE /guru/soal/{soal}        # Delete soal
```

## 💡 Tips & Tricks

### Mengganti Data Default
Edit file `database/seeders/AdminSeeder.php` untuk mengubah:
- Email admin default
- Password admin
- Guru sample

### Menambah Kelas Baru
Edit file `database/seeders/KelasSeeder.php`:
```php
Kelas::create(['nama_kelas' => 'Kelas 3 SD']);
Kelas::create(['nama_kelas' => 'Kelas 4 SD']);
// ... dst
```

Kemudian jalankan:
```bash
php artisan migrate:fresh --seed
```

### Debugging
Lihat error log di:
```
storage/logs/laravel.log
```

## 📞 Support

Jika ada masalah, cek:
1. Database connection di `.env`
2. Apache/Nginx sudah running
3. PHP version compatibility (min PHP 8.1)
4. Composer dependencies: `composer install`
5. Clear cache: `php artisan cache:clear`

## 📄 License

Educational Project - Feel free to use and modify
