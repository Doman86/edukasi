# 📊 Database Schema & API Reference

## Database ERD (Entity Relationship Diagram)

```
┌─────────────┐
│    Users    │
├─────────────┤
│ id (PK)     │
│ name        │
│ email (UQ)  │
│ password    │
│ role        │◄─────┐
│ status      │      │
│ timestamps  │      │
└─────────────┘      │
        │             │
        │1       ┌────┴──────────┐
        │        │               │
        │        ▼               │
        │   ┌─────────────┐      │
        │   │    Soal     │      │
        │   ├─────────────┤      │
        │   │ id (PK)     │      │
        │   │ user_id(FK) ├──────┘
        │   │ kelas_id(FK)├──────┐
        │   │ kategori_id(FK)   │
        │   │ pertanyaan  │      │
        │   │ pilihan_a   │      │
        │   │ pilihan_b   │      │
        │   │ pilihan_c   │      │
        │   │ pilihan_d   │      │
        │   │ jawaban_be.. │      │
        │   │ timestamps  │      │
        │   └─────────────┘      │
        │          │1    M        │
        │          │             │
        │   ┌──────┴─────┐       │
        │   │            │       │
        │   ▼            ▼       │
        │ ┌───────────┐ ┌────────┴──────┐
        │ │  Kelas    │ │   Kategori    │
        │ ├───────────┤ ├───────────────┤
        │ │ id (PK)   │ │ id (PK)       │
        │ │ nama_kelas│ │ nama_kategori │
        │ │ timestamps│ │ timestamps    │
        │ └───────────┘ └───────────────┘
        │      M             M
        │      │             │
        └──────┴─────────────┘
```

## Table Details

### 1. users
**Purpose**: Menyimpan data user (admin dan guru)

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| name | VARCHAR(255) | NOT NULL | Nama lengkap user |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email unik untuk login |
| email_verified_at | TIMESTAMP | NULLABLE | Untuk email verification (optional) |
| password | VARCHAR(255) | NOT NULL | Password hashed |
| remember_token | VARCHAR(100) | NULLABLE | Token untuk "remember me" |
| role | ENUM('admin', 'guru') | NOT NULL, DEFAULT 'guru' | Role user |
| status | ENUM('pending', 'verified', 'rejected') | NOT NULL, DEFAULT 'pending' | Status verifikasi |
| created_at | TIMESTAMP | NOT NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NOT NULL | Waktu diupdate |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE: email

**Sample Data**:
```sql
INSERT INTO users VALUES (
  1, 'Admin Edukasi', 'admin@edukasi.local', NULL, 
  '$2y$12$...', NULL, 'admin', 'verified', NOW(), NOW()
);

INSERT INTO users VALUES (
  2, 'Guru Test', 'guru@edukasi.local', NULL,
  '$2y$12$...', NULL, 'guru', 'pending', NOW(), NOW()
);
```

---

### 2. kelas
**Purpose**: Menyimpan data kelas/tingkat sekolah

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| nama_kelas | VARCHAR(255) | NOT NULL | Nama kelas (e.g., "Kelas 1 SD") |
| created_at | TIMESTAMP | NOT NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NOT NULL | Waktu diupdate |

**Indexes**:
- PRIMARY KEY: id

**Sample Data**:
```sql
INSERT INTO kelas (nama_kelas) VALUES ('Kelas 1 SD');
INSERT INTO kelas (nama_kelas) VALUES ('Kelas 2 SD');
INSERT INTO kelas (nama_kelas) VALUES ('Kelas 3 SD');
```

---

### 3. kategori
**Purpose**: Menyimpan data kategori/tingkat kesulitan soal

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| nama_kategori | ENUM('mudah', 'normal', 'sulit') | NOT NULL | Tingkat kesulitan |
| created_at | TIMESTAMP | NOT NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NOT NULL | Waktu diupdate |

**Indexes**:
- PRIMARY KEY: id

**Sample Data**:
```sql
INSERT INTO kategori (nama_kategori) VALUES ('mudah');
INSERT INTO kategori (nama_kategori) VALUES ('normal');
INSERT INTO kategori (nama_kategori) VALUES ('sulit');
```

---

### 4. soal
**Purpose**: Menyimpan data soal yang dibuat guru

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT | FK → users(id), NOT NULL | ID guru pembuat soal |
| kelas_id | BIGINT | FK → kelas(id), NOT NULL | ID kelas target soal |
| kategori_id | BIGINT | FK → kategori(id), NOT NULL | ID kategori kesulitan |
| pertanyaan | LONGTEXT | NOT NULL | Teks pertanyaan soal |
| pilihan_a | VARCHAR(255) | NOT NULL | Opsi jawaban A |
| pilihan_b | VARCHAR(255) | NOT NULL | Opsi jawaban B |
| pilihan_c | VARCHAR(255) | NOT NULL | Opsi jawaban C |
| pilihan_d | VARCHAR(255) | NOT NULL | Opsi jawaban D |
| jawaban_benar | ENUM('a', 'b', 'c', 'd') | NOT NULL | Jawaban yang benar |
| created_at | TIMESTAMP | NOT NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NOT NULL | Waktu diupdate |

**Indexes**:
- PRIMARY KEY: id
- FOREIGN KEY: user_id → users(id) ON DELETE CASCADE
- FOREIGN KEY: kelas_id → kelas(id) ON DELETE CASCADE
- FOREIGN KEY: kategori_id → kategori(id) ON DELETE CASCADE
- INDEX: user_id (untuk query soal by guru)
- INDEX: kelas_id (untuk query soal by kelas)
- INDEX: kategori_id (untuk query soal by kategori)

**Sample Data**:
```sql
INSERT INTO soal (user_id, kelas_id, kategori_id, pertanyaan, pilihan_a, 
  pilihan_b, pilihan_c, pilihan_d, jawaban_benar, created_at, updated_at)
VALUES (2, 1, 1, 'Berapa hasil 1 + 1?', '2', '3', '4', '5', 'a', NOW(), NOW());
```

---

## SQL Queries untuk Development

### Lihat semua guru yang pending verifikasi
```sql
SELECT * FROM users WHERE role = 'guru' AND status = 'pending';
```

### Lihat soal yang dibuat guru tertentu
```sql
SELECT s.*, k.nama_kelas, ka.nama_kategori, u.name as guru_name
FROM soal s
JOIN users u ON s.user_id = u.id
JOIN kelas k ON s.kelas_id = k.id
JOIN kategori ka ON s.kategori_id = ka.id
WHERE s.user_id = 2
ORDER BY s.created_at DESC;
```

### Hitung total soal per guru
```sql
SELECT u.name, COUNT(s.id) as total_soal
FROM users u
LEFT JOIN soal s ON u.id = s.user_id
WHERE u.role = 'guru' AND u.status = 'verified'
GROUP BY u.id;
```

### Hitung soal per kategori
```sql
SELECT ka.nama_kategori, COUNT(s.id) as total
FROM soal s
JOIN kategori ka ON s.kategori_id = ka.id
GROUP BY ka.id;
```

### Hitung soal per kelas
```sql
SELECT k.nama_kelas, COUNT(s.id) as total
FROM soal s
JOIN kelas k ON s.kelas_id = k.id
GROUP BY k.id;
```

### Reset user status ke pending
```sql
UPDATE users SET status = 'pending' WHERE role = 'guru';
```

### Delete semua soal dari guru tertentu
```sql
DELETE FROM soal WHERE user_id = 2;
```

---

## Relationship Queries (Eloquent)

### Get semua soal dari guru tertentu
```php
$guru = User::find(2);
$soal = $guru->soal; // atau $guru->soal()->get();
```

### Get detail soal dengan relasi
```php
$soal = Soal::with(['guru', 'kelas', 'kategori'])->first();
// Akses:
// $soal->guru->name
// $soal->kelas->nama_kelas
// $soal->kategori->nama_kategori
```

### Get soal by kelas
```php
$kelas = Kelas::find(1);
$soal = $kelas->soal; // atau $kelas->soal()->get();
```

### Get soal by kategori
```php
$kategori = Kategori::find(2);
$soal = $kategori->soal; // atau $kategori->soal()->get();
```

### Get guru verified dengan soal mereka
```php
$guru = User::with('soal')
  ->where('role', 'guru')
  ->where('status', 'verified')
  ->get();
```

---

## Foreign Key Constraints

Semua foreign key menggunakan:
- `ON DELETE CASCADE`: Jika parent dihapus, child juga dihapus
- `ON UPDATE CASCADE`: Jika parent diupdate, child juga terupdate

Contoh:
- Jika user (guru) dihapus → semua soalnya otomatis dihapus
- Jika kelas dihapus → semua soal di kelas itu otomatis dihapus
- Jika kategori dihapus → semua soal dengan kategori itu otomatis dihapus

---

## Backup & Restore

### Export Database
```bash
mysqldump -u root db_edukasi > backup.sql
```

### Import Database
```bash
mysql -u root db_edukasi < backup.sql
```

---

## Performance Tips

1. **Indexing**: Jika soal banyak, tambahkan index:
```sql
CREATE INDEX idx_soal_user ON soal(user_id);
CREATE INDEX idx_soal_kelas ON soal(kelas_id);
CREATE INDEX idx_soal_kategori ON soal(kategori_id);
```

2. **Pagination**: Gunakan pagination untuk list soal:
```php
$soal = Soal::paginate(10);
```

3. **Eager Loading**: Gunakan with() untuk mencegah N+1 query:
```php
// ❌ Bad - N+1 queries
foreach ($soal as $s) {
  echo $s->guru->name; // Query untuk setiap soal
}

// ✅ Good - 1 query
$soal = Soal::with('guru')->get();
foreach ($soal as $s) {
  echo $s->guru->name; // Data sudah loaded
}
```

---

Last Updated: 2026-07-23
