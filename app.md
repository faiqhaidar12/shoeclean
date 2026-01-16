# 👟 Shoe Cleaning App - Multi Outlet System

## 📌 Konsep Utama

Aplikasi cuci sepatu berbasis web dengan sistem **multi-outlet** dimana satu pemilik usaha dapat memiliki banyak cabang, dan setiap outlet memiliki admin/staff sendiri.

---

## 👥 Hierarki Role & Akses

### 1. **Owner (Pemilik Usaha)**

-   Melihat dashboard gabungan semua outlet
-   Membuat & mengelola outlet baru
-   Menunjuk Admin untuk setiap outlet
-   Melihat laporan keuangan semua outlet
-   Perbandingan performa antar outlet
-   Melihat semua transaksi dari semua outlet

### 2. **Admin Outlet**

-   Mengelola 1 outlet yang ditugaskan
-   CRUD order di outlet tersebut
-   Mengelola staff di outlet tersebut
-   Melihat laporan khusus outletnya saja
-   Mengelola layanan & harga outlet

### 3. **Staff/Kasir**

-   Input order baru
-   Update status order
-   Terima pembayaran
-   Tidak bisa akses laporan/setting

---

## 🏪 Modul Multi-Outlet

### Fitur Owner Dashboard

-   **Overview Semua Outlet** - Total pendapatan, total order, outlet terlaris
-   **Perbandingan Outlet** - Grafik performa antar outlet
-   **Switch Outlet** - Lihat detail per outlet
-   **Manajemen Outlet** - Tambah/edit/nonaktifkan outlet
-   **Assign Admin** - Tugaskan admin ke outlet

### Fitur Per Outlet

-   Nama, alamat, kontak outlet
-   Jam operasional
-   Status aktif/nonaktif
-   Layanan & harga spesifik outlet
-   Staff yang bertugas

---

## 📦 Modul Order & Operasional

### Manajemen Order

-   CRUD order dengan status: `pending → proses → selesai → diambil`
-   Assign order ke outlet tertentu
-   Invoice dengan kode outlet
-   Riwayat order per outlet

### Manajemen Layanan (Cuci Sepatu)

-   Cuci Standar (Regular Cleaning)
-   Deep Clean
-   Deep Clean + Whitening
-   Treatment Kulit (Leather Care)
-   Repaint/Pewarnaan Ulang
-   Unyellowing
-   Harga per pasang sepatu

### Manajemen Pelanggan

-   Data pelanggan + history order
-   Pelanggan bisa order di outlet manapun
-   Tracking lintas outlet

### Laporan Keuangan

-   Laporan per outlet dan gabungan
-   Export PDF/Excel
-   Filter tanggal, outlet, layanan

### Manajemen Pengeluaran

-   Catat biaya operasional per outlet
-   Kategori: bahan cuci, listrik, gaji, dll

---

## 📱 Modul Pelanggan (Tanpa Login)

-   **Tracking Order** - Cek status via nomor invoice
-   **Notifikasi WhatsApp** - Update otomatis saat status berubah
-   **Estimasi Waktu** - Perkiraan selesai
-   **Riwayat Order** - Akses via nomor HP
-   **Info Outlet** - Lihat alamat & kontak outlet

---

## 💳 Modul Pembayaran

-   **Midtrans** - QRIS, VA, E-Wallet
-   **Tunai** - Catat manual oleh kasir
-   **DP/Cicilan** - Bayar sebagian
-   **Invoice Digital** - Kirim via WhatsApp

---

## ⭐ Fitur Tambahan

-   **Pickup & Delivery** - Jasa antar-jemput (per outlet)
-   **Promo & Diskon** - Voucher, member discount
-   **Review/Rating** - Per outlet
-   **Reminder** - Notifikasi sepatu belum diambil
-   **QR Code** - Scan untuk tracking cepat

---

## 🗄️ Struktur Database Utama

```
users
├── id, name, email, password, role (owner/admin/staff)
└── outlet_id (null untuk owner)

outlets
├── id, owner_id, name, address, phone
├── status (active/inactive)
└── created_at

services
├── id, outlet_id, name, price, unit (pasang)
└── is_active

customers
├── id, name, phone, address
└── created_at

orders
├── id, outlet_id, customer_id, invoice_number
├── status, total_price, payment_status
├── estimated_finish, picked_up_at
└── created_by (user_id)

order_items
├── id, order_id, service_id
├── qty, price, subtotal
└── notes

payments
├── id, order_id, amount, method
├── midtrans_transaction_id
└── paid_at

expenses
├── id, outlet_id, category, amount
├── description
└── date
```

---

## 🛠️ Tech Stack

| Komponen   | Teknologi             |
| ---------- | --------------------- |
| Backend    | Laravel 12            |
| Frontend   | Livewire + Alpine.js  |
| CSS        | Tailwind CSS          |
| Database   | MySQL                 |
| Payment    | Midtrans              |
| Notifikasi | Fonnte (WhatsApp API) |
| Charts     | Chart.js              |
