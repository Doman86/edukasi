# 🚀 Quick Start Guide - Web Edukasi

## Langkah 1: Setup Database
```bash
cd d:\laragon\www\web-edukasi
php artisan migrate:fresh --seed --force
```

## Langkah 2: Jalankan Server
```bash
php artisan serve
# Atau akses melalui: http://localhost/web-edukasi
```

## Langkah 3: Login & Coba Fitur

### A. Test sebagai ADMIN
```
URL: http://localhost:8000/login
Email: admin@edukasi.local
Password: password123

✓ Bisa verifikasi guru baru
✓ Melihat guru yang sudah verified
```

### B. Test sebagai GURU (Sebelum Verified)
```
URL: http://localhost:8000/login
Email: guru@edukasi.local
Password: password123

⚠️ Akan melihat pesan "Akun menunggu verifikasi"
```

### C. Verifikasi Guru dengan Admin
```
1. Login sebagai admin
2. Lihat "Guru Menunggu Verifikasi"
3. Klik tombol "Verifikasi" untuk guru@edukasi.local
4. Logout
```

### D. Akses Fitur Guru (Setelah Verified)
```
Login lagi dengan guru@edukasi.local
✓ Bisa buat soal baru
✓ Bisa lihat daftar soal
✓ Bisa edit/hapus soal
```

## Langkah 4: Buat Soal Pertama

1. Login sebagai guru (setelah verified)
2. Klik "Tambah Soal" atau akses `/guru/soal/create`
3. Isi form:
   - Kelas: Kelas 1 SD
   - Tingkat: Mudah
   - Pertanyaan: "Berapa hasil 1 + 1?"
   - Pilihan A: 2
   - Pilihan B: 3
   - Pilihan C: 4
   - Pilihan D: 5
   - Jawaban Benar: A
4. Klik "Simpan Soal"

## File-File Penting

| File | Fungsi |
|------|--------|
| `app/Http/Controllers/AuthController.php` | Login & Register |
| `app/Http/Controllers/AdminController.php` | Dashboard Admin |
| `app/Http/Controllers/GuruController.php` | Dashboard & Soal Guru |
| `app/Http/Middleware/AdminMiddleware.php` | Proteksi Admin |
| `app/Http/Middleware/GuruMiddleware.php` | Proteksi Guru |
| `routes/web.php` | Semua routes |
| `database/seeders/AdminSeeder.php` | Data default |

## Troubleshooting

### Error: "SQLSTATE[HY000]: General error"
```bash
# Clear config & cache
php artisan config:clear
php artisan cache:clear
php artisan migrate:fresh --seed --force
```

### Error: "Target [auth] is not instantiable"
```bash
composer install
php artisan migrate:fresh --seed --force
```

### Halaman tampil kosong
```bash
# Cek permission
chmod -R 755 storage bootstrap/cache
php artisan cache:clear
```

## Next Steps

Setelah berhasil test, Anda bisa:

1. **Tambah Kelas Baru**
   - Edit `database/seeders/KelasSeeder.php`
   - Jalankan `php artisan migrate:fresh --seed`

2. **Tambah User Admin Lain**
   - Register dengan role admin
   - Atau edit AdminSeeder.php

3. **Customize UI**
   - Modifikasi `.blade.php` files di `resources/views/`
   - Tailwind CSS sudah included

4. **Tambah Fitur**
   - Buat controller baru: `php artisan make:controller NamaController`
   - Buat model baru: `php artisan make:model NamaModel -m`

## Contact & Support

Jika ada yang tidak jelas, silakan tanyakan atau lihat PANDUAN_LENGKAP.md
