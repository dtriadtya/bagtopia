# Product Requirement Document (PRD)
## Reseller Monitoring & Marketplace Integration System

**Versi:** 1.1  
**Tanggal:** April 2026  
**Status:** Draft untuk Review Client

---

## 1. Latar Belakang

Client memiliki bisnis penjualan tas dengan jaringan reseller yang berjualan melalui marketplace Shopee dan TikTok Shop. Saat ini client menghadapi tantangan dalam:

- Monitoring performa reseller secara terpusat
- Validasi keaslian order dari berbagai toko
- Rekap data penjualan lintas platform
- Identifikasi potensi fraud seperti duplikasi pembeli atau cancel rate tinggi

Sistem ini dibangun untuk menjawab kebutuhan tersebut melalui satu dashboard terpusat.

---

## 2. Tujuan Sistem

- Mengintegrasikan data order dari Shopee dan TikTok Shop ke dalam satu platform
- Memantau aktivitas dan performa seluruh reseller secara real-time
- Mengelola dan memvalidasi keaslian setiap order
- Mendeteksi potensi fraud secara otomatis
- Menyediakan laporan penjualan harian, bulanan, dan tahunan

---

## 3. Ruang Lingkup

### 3.1 Dalam Scope
- Dashboard admin untuk monitoring reseller
- Manajemen data reseller (tambah, edit, nonaktifkan)
- Import data order via CSV (Phase 1)
- Integrasi API Shopee & TikTok Shop via OAuth (Phase 2–3)
- Sistem fraud detection berbasis scoring
- Laporan & analitik penjualan

### 3.2 Di Luar Scope
- Payment gateway
- Platform marketplace milik sendiri
- Aplikasi mobile (dipertimbangkan di fase berikutnya)

---

## 4. Pengguna Sistem

### 4.1 Admin (Owner/Client)
Memiliki akses penuh ke seluruh data dan konfigurasi sistem.

**Dapat melakukan:**
- Mengelola daftar reseller
- Melihat semua order dari semua reseller
- Mengakses laporan penjualan lengkap
- Melihat dan mengelola flag fraud
- Menambahkan/menghubungkan toko marketplace reseller

### 4.2 Reseller
Memiliki akses terbatas hanya ke data toko dan order milik sendiri.

**Dapat melakukan:**
- Melihat performa penjualan pribadi
- Melihat status order di toko miliknya
- Menghubungkan akun marketplace (jika diberi izin)

---

## 5. Kebutuhan Fungsional

### 5.1 Manajemen Reseller
- Tambah, edit, dan nonaktifkan reseller
- Setiap reseller dapat memiliki lebih dari satu toko (Shopee + TikTok Shop)
- Sistem level reseller: Bronze, Silver, Gold berdasarkan volume penjualan

### 5.2 Integrasi Marketplace

#### Phase 1 — Import CSV (MVP)
- Reseller mengekspor data order secara manual dari Shopee/TikTok Shop
- Admin atau reseller mengupload file CSV ke sistem
- Sistem memproses dan menyimpan data order secara otomatis
- Format CSV mengikuti standar export bawaan Shopee dan TikTok Shop

#### Phase 2 & 3 — Integrasi API Otomatis
- Koneksi OAuth ke akun toko reseller di Shopee dan TikTok Shop
- Token tersimpan terenkripsi di database
- Scheduler otomatis menarik data order setiap jam
- Data yang disinkronisasi: order, status pengiriman, produk terjual, data pembeli

### 5.3 Monitoring Order

Data yang ditampilkan per order:
- Nama dan nomor HP pembeli
- Alamat pengiriman lengkap
- Produk yang dibeli beserta jumlah dan harga
- Total nilai transaksi
- Status order dan pengiriman
- Platform asal order (Shopee / TikTok Shop)
- Nama toko dan reseller

Filter yang tersedia:
- Rentang tanggal
- Reseller tertentu
- Platform (Shopee / TikTok Shop)
- Status fraud

### 5.4 Fraud Detection

Sistem menjalankan pengecekan otomatis setiap kali order masuk dengan mekanisme scoring sebagai berikut:

| Rule | Poin |
|------|------|
| Nomor HP pembeli sama dengan order lain | +1 |
| Alamat pengiriman sama dengan order lain | +1 |
| Cancel rate reseller tinggi (>20%) | +2 |

**Kategori hasil:**

| Skor | Status |
|------|--------|
| 0–1 | Valid |
| 2–3 | Suspicious |
| >3 | Fraud |

Setiap flag fraud dicatat di log audit untuk keperluan investigasi.

### 5.5 Laporan & Analitik
- Total penjualan per periode (harian, mingguan, bulanan, tahunan)
- Penjualan per reseller dan perbandingan performa
- Penjualan per platform (Shopee vs TikTok Shop)
- Grafik tren penjualan
- Laporan fraud: jumlah order suspicious dan fraud per periode
- Export laporan ke CSV atau PDF

---

## 6. Kebutuhan Non-Fungsional

| Aspek | Target |
|-------|--------|
| Performa | Mampu memproses 10.000+ order per hari |
| Keamanan | Token marketplace dienkripsi di database |
| Ketersediaan | Uptime 99% |
| Skalabilitas | Arsitektur modular, mudah ditambah fitur baru |

---

## 7. Arsitektur Sistem

```
Marketplace (Shopee, TikTok Shop)
         ↓ API / CSV Upload
    Backend (Laravel)
         ↓
    Queue & Scheduler (Redis)
         ↓
    Database (MySQL)
         ↓
    Dashboard Admin (Filament)
```

---

## 8. Desain Database

### Tabel Utama

| Tabel | Fungsi |
|-------|--------|
| `resellers` | Data master reseller |
| `reseller_tiers` | Level reseller (Bronze/Silver/Gold) |
| `stores` | Akun marketplace per reseller, menyimpan token OAuth |
| `orders` | Semua order lintas platform, termasuk fraud score |
| `order_items` | Detail produk per order |
| `product_mappings` | Mapping SKU internal vs SKU marketplace |
| `fraud_logs` | Log audit tiap rule fraud yang terpicu |
| `sync_logs` | Riwayat sinkronisasi data dari marketplace |
| `notifications` | Alert sistem untuk admin dan reseller |

---

## 9. Alur Data

1. **Phase 1 (CSV):** Reseller mengekspor order dari marketplace → upload CSV ke sistem → sistem parsing dan simpan ke database → fraud detection dijalankan → data tampil di dashboard
2. **Phase 2–3 (API):** Toko reseller terhubung via OAuth → scheduler otomatis tarik data setiap jam → data disimpan ke database → fraud detection dijalankan → data tampil di dashboard real-time

---

## 10. Rencana Implementasi

### Phase 1 — MVP (Estimasi: 3–4 minggu)
- Manajemen reseller (CRUD)
- Import order via CSV
- Dashboard monitoring basic
- Fraud detection dengan scoring
- Laporan penjualan dasar

**Catatan penting:** Pendaftaran developer account ke Shopee Open Platform dan TikTok Shop Partner harus dilakukan di minggu pertama Phase 1, karena proses review dan approval dapat memakan waktu 2–4 minggu.

### Phase 2 — Integrasi Shopee (Estimasi: 2–3 minggu setelah approval)
- OAuth connection ke Shopee
- Auto-sync order dan produk dari Shopee
- Token management dan auto-refresh

### Phase 3 — Integrasi TikTok Shop (Estimasi: 2–3 minggu setelah approval)
- OAuth connection ke TikTok Shop
- Auto-sync order dan produk dari TikTok Shop
- Notifikasi real-time untuk order baru

### Phase 4 — Optimasi & Peningkatan
- Laporan advanced dengan export PDF
- Peningkatan performa untuk volume tinggi
- Fitur tambahan berdasarkan feedback client

---

## 11. Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel (PHP) |
| Database | MySQL |
| Frontend / Dashboard | Filament Admin Panel |
| Queue & Job | Redis + Laravel Queue |
| Scheduler | Laravel Cron |
| Server | VPS / Cloud (disarankan) |

---

## 12. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| Approval API marketplace terlambat | Phase 2–3 tertunda | Mulai dengan CSV di Phase 1; daftar developer account di hari pertama |
| Rate limit API marketplace | Data tidak lengkap | Implementasi queue dan retry mechanism |
| Inkonsistensi format data antar platform | Bug parsing | Normalisasi data sebelum disimpan ke database |
| Token OAuth expired | Sinkronisasi berhenti | Auto-refresh token + notifikasi jika refresh gagal |

---

## 13. Metrik Keberhasilan

- 100% reseller terdaftar dan toko terkoneksi ke sistem
- 90% data order masuk secara otomatis (setelah Phase 2–3 aktif)
- Waktu deteksi fraud < 1 menit setelah order masuk
- Penurunan order fraud terverifikasi dibanding sebelum sistem berjalan

---

## 14. Kesimpulan

Sistem ini memungkinkan client untuk memantau seluruh aktivitas penjualan reseller dari satu dashboard terpusat, mengurangi ketergantungan pada laporan manual, dan meminimalisir risiko penipuan. Pendekatan bertahap (CSV terlebih dahulu, lalu API) memastikan sistem dapat segera digunakan sambil proses integrasi marketplace berlangsung.

---

*Dokumen ini bersifat draft dan terbuka untuk diskusi lebih lanjut.*
