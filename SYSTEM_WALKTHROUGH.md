# Bagtopedia Reseller Monitoring - Walkthrough Sistem

Dokumen ini menjelaskan isi sistem, alur penggunaan, serta fitur yang sudah tersedia saat ini.

## 1. Ringkasan Sistem

Sistem ini adalah dashboard monitoring reseller berbasis Laravel + MySQL untuk:

- monitoring penjualan reseller lintas platform (Shopee/TikTok Shop),
- deteksi fraud berbasis scoring,
- import order dari CSV,
- pelaporan transaksi dan export CSV.

## 2. Stack yang Digunakan

- Backend: Laravel (PHP)
- Database: MySQL
- Frontend: Blade + Tailwind CSS (via Vite)
- Development server: `php artisan serve`

## 3. Struktur Menu Sidebar

- Dashboard
- Reseller
- Orders
- Fraud Detection
- Import Data
- Reports
- Settings (placeholder UI)

## 4. Modul yang Sudah Ada

### 4.1 Dashboard

Menampilkan ringkasan utama:

- total reseller,
- total order,
- total revenue,
- fraud alerts,
- recent orders,
- top resellers,
- sales by platform,
- recent import logs.

### 4.2 Reseller Management

Fitur:

- tambah reseller,
- edit reseller,
- nonaktifkan reseller,
- lihat detail reseller (store + order terbaru).

Data reseller terhubung dengan tier reseller (Bronze/Silver/Gold).

### 4.3 Orders

Fitur:

- list order,
- filter order (tanggal, reseller, platform, fraud status),
- input order manual (backup).

### 4.4 Import CSV (Core MVP)

Fitur:

- upload file CSV order,
- pilih reseller, store, platform,
- parsing data order,
- simpan ke `orders` + `order_items`,
- deteksi fraud otomatis,
- pencatatan `fraud_logs`,
- pencatatan `import_logs`.

Tambahan:

- download template CSV Shopee,
- download template CSV TikTok Shop.

### 4.5 Fraud Detection

Fraud status dihitung saat order masuk:

- rule 1: nomor HP pembeli sama (+1)
- rule 2: alamat pengiriman sama (+1)
- rule 3: cancel rate reseller > 20% (+2)

Kategori:

- skor 0-1: `valid`
- skor 2-3: `suspicious`
- skor >3: `fraud`

### 4.6 Reports

Fitur:

- filter laporan berdasarkan tanggal,
- filter platform, fraud status, keyword transaksi,
- ringkasan KPI periode,
- sales by platform,
- top reseller by period,
- tabel laporan transaksi,
- export laporan transaksi ke CSV (mengikuti filter aktif).

## 5. Tabel Database Utama

Sudah dibuat:

- `reseller_tiers`
- `resellers`
- `stores`
- `orders`
- `order_items`
- `fraud_logs`
- `import_logs`

## 6. Route Utama (Ringkas)

- `/` -> Dashboard
- `/resellers` -> CRUD Reseller
- `/orders` -> Daftar Order
- `/orders/create` -> Import/Manual Input
- `/orders/import-csv` -> Proses import CSV
- `/orders/template-csv` -> Download template CSV
- `/reports` -> Halaman laporan
- `/reports/export` -> Export CSV laporan

## 7. Alur Penggunaan (Walkthrough Cepat)

### Langkah 1 - Setup

1. Jalankan MySQL.
2. Pastikan konfigurasi `.env` benar (`DB_*`).
3. Jalankan migration:
   - `php artisan migrate`
4. Jalankan server:
   - `php artisan serve`

### Langkah 2 - Siapkan Master Data

1. Masuk menu **Reseller**.
2. Tambahkan reseller.
3. Pastikan store reseller sudah tersedia di data store.

### Langkah 3 - Import Data Order

1. Masuk menu **Import Data**.
2. Download template CSV sesuai platform.
3. Isi data sesuai format template.
4. Upload CSV dan jalankan import.
5. Cek hasil di notifikasi + dashboard (Recent Import Logs).

### Langkah 4 - Monitoring Fraud

1. Cek **Fraud Alerts** di Dashboard.
2. Buka menu **Fraud Detection** untuk melihat order terindikasi fraud.

### Langkah 5 - Buat Laporan

1. Masuk menu **Reports**.
2. Terapkan filter periode/platform/status.
3. Review tabel transaksi.
4. Klik **Export CSV** untuk unduh data.

## 8. Batasan Saat Ini

Yang belum tersedia penuh:

- OAuth API Shopee/TikTok (masih fase berikutnya),
- scheduler auto-sync marketplace,
- role-based access (Admin vs Reseller),
- halaman Settings fungsional,
- export PDF report.

## 9. Catatan Pengembangan Lanjutan

Prioritas yang direkomendasikan:

1. CRUD Store di UI (jika belum lengkap operasional),
2. role & permission,
3. integrasi API marketplace,
4. sync log + notifikasi kegagalan sinkronisasi,
5. export PDF dan chart analytics lebih lanjut.

