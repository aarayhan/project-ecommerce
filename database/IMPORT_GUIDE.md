# 📋 Cara Import Database ke InfinityFree

## Langkah-langkah Import via phpMyAdmin

### 1. Login ke InfinityFree Control Panel
- Masuk ke dashboard InfinityFree Anda
- Klik "MySQL Databases"

### 2. Buat Database Baru
- Klik "Create Database"
- Beri nama database (contoh: `padviolett`)
- Catat informasi database:
  - Database Name: `epiz_xxxxx_padviolett`
  - Username: `epiz_xxxxx`
  - Password: (yang Anda buat)
  - Hostname: `sql200.infinityfree.com`

### 3. Akses phpMyAdmin
- Di control panel, klik "phpMyAdmin"
- Login dengan kredensial database
- Pilih database yang baru dibuat

### 4. Import File SQL
- Klik tab "Import"
- Klik "Choose File" dan pilih `padviolett_database.sql`
- Pastikan format: SQL
- Klik "Go" untuk mulai import

### 5. Verifikasi Import
Setelah import berhasil, Anda akan melihat:
- ✅ Table `user` dengan 3 data user
- ✅ Table `books` dengan 15 data buku sample

## Update Konfigurasi Website

Edit file `config/database.php`:

```php
<?php
$host = 'sql200.infinityfree.com';     // Sesuai yang diberikan
$dbname = 'epiz_xxxxx_padviolett';     // Nama database Anda
$username = 'epiz_xxxxx';              // Username database
$password = 'your_password';           // Password database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## Login Credentials

Setelah database berhasil di-import, Anda bisa login dengan:

**Admin:**
- Email: `admin@padviolett.com`
- Password: `password`

**User Demo:**
- Email: `user@padviolett.com`
- Password: `password`

**User Demo 2:**
- Email: `demo@padviolett.com`
- Password: `password`

## Troubleshooting

### Error saat Import
- Pastikan file SQL tidak corrupt
- Coba import ulang
- Periksa size limit phpMyAdmin (biasanya 50MB)

### Database Connection Error
- Periksa kredensial database
- Pastikan hostname benar
- Test koneksi via script PHP sederhana

### Tabel Tidak Muncul
- Refresh phpMyAdmin
- Periksa apakah import benar-benar selesai
- Cek error log di control panel

## Data Sample

Database sudah berisi:
- **15 buku sample** dengan berbagai kategori
- **3 user account** (1 admin, 2 user)
- **Cover images** menggunakan URL eksternal
- **Multiple categories** per buku

Siap untuk testing dan demo!