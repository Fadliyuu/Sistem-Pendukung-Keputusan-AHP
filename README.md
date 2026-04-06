# Sistem Pendukung Keputusan AHP – PT Telkom Satelit Indonesia Regional 6

Stack: PHP Native + Firestore + Bootstrap 5 + Dompdf  
Lokasi workspace: `C:\Users\Fadd\OneDrive\Documents\web juanda`

## Persiapan
1. Install PHP 8.1+ dan Composer.
2. Buat project Firebase → service account → unduh `service-account.json` dan letakkan di root project.
3. Salin `.env.example` ke `.env` lalu isi:
```
FIRESTORE_PROJECT_ID=your-project-id
FIRESTORE_KEY_FILE=C:\Users\Fadd\OneDrive\Documents\web juanda\service-account.json
FIRESTORE_PREFIX=ahp_
ADMIN_DEFAULT_USER=admin
ADMIN_DEFAULT_PASS=admin123
```
4. Install dependency:
```
composer install
```

## Menjalankan
```
php -S localhost:8000 -t public
```
Lalu buka http://localhost:8000  
Login admin default: `admin/admin123` (auto dibuat saat login pertama).  
Login pegawai default: `pegawai/pegawai123` (auto dibuat saat login pertama).

## Fitur
- Auth: login, logout (role admin/pegawai, sesi).
- Admin: dashboard, CRUD pegawai & kriteria, input pairwise AHP, hitung bobot, input nilai pegawai, ranking, laporan PDF.
- Pegawai: lihat ranking.
- Validasi dasar (required, range), flash message.

## Arsitektur
- `public/index.php` : front controller & router sederhana.
- `src/Firestore.php` : koneksi Firestore (prefix koleksi).
- `src/Ahp.php` : perhitungan AHP (normalisasi, bobot, λmax, CI, CR, ranking).
- `src/Auth.php` : login & seeding admin default.
- `controllers/*.php` : logika per modul.
- `views/*.php` : tampilan dengan Bootstrap 5.

## Koleksi Firestore
- `ahp_users` (username, password_hash, role, nama)
- `ahp_employees` (id_pegawai, nama_pegawai, jabatan, divisi, masa_kerja)
- `ahp_criteria` (id_kriteria, nama_kriteria, jenis_kriteria, deskripsi)
- `ahp_pairwise` (document `current` berisi matrix)
- `ahp_scores` (doc per pegawai: index kriteria => nilai)
- `ahp_results` (doc `current`: weights, lambda, ci, cr, status)

## Catatan
- Pastikan service account Firestore memiliki akses read/write.
- Jika CR > 0.1, ulangi input pairwise agar konsisten.
- Dompdf dipakai untuk export laporan; pastikan extension font bawaan cukup.
