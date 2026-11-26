# e-Secretariat

> Platform digital sekretariat untuk mengelola **laporan**, **persuratan & arsip**, **pengguna**, dan **jejak aktivitas** dalam satu tempat.

Aplikasi ini dibuat sebagai solusi terintegrasi bagi sekretariat organisasi/instansi: alur kerja laporan dengan persetujuan berjenjang, pencatatan surat masuk dan keluar, arsip digital dengan unggah berkas, pencarian global, ekspor data, serta tampilan siap cetak.

---

## ✨ Fitur Utama

### 📊 Dashboard Analitik
- Ringkasan total laporan, laporan menunggu review, surat baru, dan surat bulan ini.
- Grafik tren laporan & surat 12 bulan (SVG interaktif, tanpa library eksternal).
- Distribusi status laporan (disetujui, menunggu, ditolak).
- Daftar laporan terbaru, surat terbaru, dan aktivitas terakhir.

### 📄 Manajemen Laporan
- Buat, ubah, tinjau, dan hapus laporan bulanan/tahunan.
- Alur persetujuan: `Menunggu Review` → `Disetujui` / `Ditolak`.
- Catatan review wajib diisi saat menolak laporan.
- Unggah hingga 5 lampiran (PDF, DOC/DOCX, XLS/XLSX, JPG, PNG) dengan pratinjau file.
- Halaman cetak laporan siap tanda tangan (print-friendly).
- Ekspor daftar laporan ke CSV (mendukung filter aktif).

### 📬 Persuratan & Arsip
- Pencatatan surat masuk dan surat keluar.
- Nomor surat otomatis dengan format sekretariat.
- Klasifikasi surat: Undangan, Nota Dinas, Surat Tugas, Surat Edaran, dan lainnya.
- Status tindak lanjut: `Baru` → `Diproses` → `Selesai` → `Diarsipkan`.
- Unggah dan unduh berkas surat digital.
- Pencarian berdasarkan nomor, judul, pengirim/penerima, dan klasifikasi.
- Ekspor arsip surat ke CSV.

### 🔍 Pencarian Global
- Satu kotak pencarian untuk laporan dan surat.
- Mencari judul, isi laporan, nomor surat, pengirim/penerima, dan keterangan.
- Pintasan keyboard `/` untuk fokus ke kolom pencarian.

### 👥 Manajemen Pengguna (Admin)
- Tambah, ubah, aktifkan/nonaktifkan, dan hapus pengguna.
- Peran `Admin`, `Operator Subbagian`, `Verifikator/PPK`, dan `Pimpinan/Sekretaris`.
- Operator menginput data dan mengunggah SPJ; verifikator memeriksa laporan dan status kendala; pimpinan hanya melihat dashboard dan laporan.
- Proteksi: admin tidak dapat menonaktifkan akun sendiri dan akun yang memiliki riwayat data tidak dapat dihapus (dapat dinonaktifkan).

### 🕘 Riwayat Aktivitas
- Audit trail otomatis untuk login, pembuatan/perubahan/penghapusan data, persetujuan, ekspor, dan unduhan.
- Filter berdasarkan jenis aktivitas dan kata kunci.

### 🎨 Antarmuka
- Desain modern dengan Tailwind CSS v4, font Plus Jakarta Sans.
- Responsif untuk desktop, tablet, dan ponsel.
- Sidebar, dropdown, toast, modal konfirmasi, dan animasi halus tanpa framework JS tambahan.
- Komponen Blade reusable: ikon SVG, avatar, badge status, page header, empty state.

---

## 🧰 Teknologi

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Database | MySQL / MariaDB |
| Frontend | Blade, Tailwind CSS v4, Vite 7, JavaScript vanilla |
| Autentikasi | Laravel Auth + middleware peran |
| Pengujian | PHPUnit / Laravel Test (SQLite in-memory) |

---

## 🔐 Akun Awal

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | `admin@sekretariat.go.id` | `password` |

Admin dapat membuat akun Operator, Verifikator, dan Pimpinan melalui menu **Manajemen Pengguna**. Semua akun aktif dapat masuk; akses fitur dibatasi berdasarkan peran. Ganti kata sandi awal sebelum aplikasi digunakan bersama.

---

## 🚀 Cara Menjalankan

### Prasyarat
- PHP **8.2+** dengan ekstensi `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`
- Composer 2
- Node.js **20+** dan npm
- MySQL/MariaDB (XAMPP, Laragon, MySQL Server, dsb.)

### Langkah Instalasi

```bash
# 1. Install dependensi backend
composer install

# 2. Salin konfigurasi lingkungan
cp .env.example .env        # Windows: copy .env.example .env

# 3. Buat application key
php artisan key:generate

# 4. Sesuaikan koneksi database pada .env
#    DB_DATABASE=db_secretariat
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Buat database kosong bernama db_secretariat
#    (phpMyAdmin / MySQL CLI / Laragon)

# 6. Jalankan migrasi + data demo
php artisan migrate:fresh --seed

# 7. Hubungkan penyimpanan publik (untuk lampiran)
php artisan storage:link

# 8. Install & build aset frontend
npm install
npm run build

# 9. Jalankan server
php artisan serve
```

Buka **http://localhost:8000** lalu masuk dengan akun demo di atas.

### Mode Pengembangan (opsional)

```bash
npm run dev
php artisan serve
```

### Deploy ke Vercel

Aplikasi Laravel tidak bisa di-deploy sebagai static site. Repository ini sudah menyediakan `Dockerfile`, `Caddyfile`, dan `vercel.json` (FrankenPHP + container service), jadi cukup import repository ke Vercel dengan pengaturan default — **jangan** mengubah *Output Directory* ke `dist` atau `public/build`.

Sebelum deploy, siapkan environment variable berikut di Vercel (Project Settings → Environment Variables):

| Variable | Contoh / Catatan |
|---|---|
| `APP_KEY` | Disarankan diisi dengan nilai tetap dari `php artisan key:generate --show`. Jika kosong, image akan membuat key otomatis (cookie user akan reset tiap rebuild image) |
| `APP_URL` | `https://nama-project.vercel.app` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `LOG_CHANNEL` | `stderr` ( agar log masuk ke Vercel Runtime Logs ) |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Kredensial database eksternal (Vercel tidak menyediakan MySQL lokal) |
| `SESSION_DRIVER` | `cookie` (default image, tidak butuh storage/database) |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |

Setelah deploy pertama, jalankan migrasi sekali terhadap database produksi:

```bash
vercel env pull .env.production
php artisan migrate --force --env=production
```

> **Catatan penyimpanan:** container Vercel bersifat stateless, sehingga file yang diunggah ke disk lokal (`storage/app/public`) tidak awet antar instance/deploy. Untuk produksi, gunakan object storage eksternal (misalnya Vercel Blob atau S3) untuk lampiran dan berkas surat.

### Menjalankan Pengujian

```bash
php artisan test
```

Pengujian mencakup autentikasi, pembuatan & validasi laporan, alur persetujuan, hak akses staf/admin, pembuatan surat, perubahan status surat, serta manajemen pengguna.

---

## 🗂️ Struktur Penting

```
app/
├── Http/
│   ├── Controllers/       # Auth, Dashboard, Report, Letter, User, Profile, Search, Activity
│   └── Middleware/        # EnsureUserIsAdmin
├── Models/                # User, Report, ReportAttachment, Letter, Activity
├── Policies/              # ReportPolicy, UserPolicy
└── Support/               # ActivityLogger

resources/
├── css/app.css            # Design system Tailwind
├── js/app.js              # Interaksi UI (sidebar, dropdown, upload, dsb.)
└── views/
    ├── auth/              # Login & registrasi
    ├── layouts/           # Shell aplikasi
    ├── dashboard.blade.php
    ├── reports/           # Index, form, detail, cetak
    ├── letters/           # Index, form, detail
    ├── users/             # Manajemen pengguna (admin)
    ├── profile/           # Profil & keamanan akun
    ├── activities/        # Audit trail
    ├── search/            # Pencarian global
    └── components/        # Komponen Blade reusable

database/
├── migrations/            # Skema users, reports, letters, activities, dll.
└── seeders/               # Data demo realistis + PDF contoh
```

---

## 🔄 Alur Kerja Singkat

1. **Staf** membuat laporan → status otomatis `Menunggu Review`.
2. **Administrator** membuka detail laporan → menyetujui atau menolak dengan catatan.
3. Laporan yang disetujui dapat dicetak atau diekspor.
4. Surat masuk/keluar dicatat, diberi klasifikasi, dan status tindak lanjut diperbarui hingga `Selesai`/`Diarsipkan`.
5. Semua aktivitas penting tercatat otomatis pada **Riwayat Aktivitas**.

---

## 🛡️ Catatan Keamanan

- Kata sandi di-hash dengan bcrypt.
- CSRF protection aktif pada seluruh form.
- Otorisasi berbasis policy + middleware peran.
- Validasi unggahan berkas: tipe MIME dan ukuran maksimal 10 MB per file.
- Akun nonaktif tidak dapat login.

---

## 📋 Checklist Demo Lomba

- [x] Login admin & staf (termasuk tombol demo)
- [x] Dashboard dengan statistik dan grafik
- [x] CRUD laporan + unggah lampiran + cetak
- [x] Persetujuan/penolakan laporan oleh admin
- [x] CRUD surat + unduh berkas + ubah status
- [x] Pencarian global
- [x] Manajemen pengguna
- [x] Profil & ganti kata sandi
- [x] Riwayat aktivitas
- [x] Ekspor CSV
- [x] Responsif & tanpa error 500
- [x] 30 pengujian otomatis lulus

---

Dibangun dengan ❤️ menggunakan Laravel dan Tailwind CSS untuk sekretariat yang lebih cepat, rapi, dan transparan.
