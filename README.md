# 🌌 Luminosity Noodles — PHP MVC Web App

Website food ordering bertema dark cosmic dengan arsitektur MVC, REST API, dan full security.

---

## 📁 Struktur Folder

```
luminosity-noodles/
├── app/
│   ├── controllers/        ← Logika bisnis (Auth, Menu, Cart, Order, Admin, Api)
│   ├── models/             ← Akses database (User, Menu, Order)
│   └── views/              ← Template HTML per fitur
│       ├── auth/           ← login.php, register.php
│       ├── menu/           ← index.php
│       ├── cart/           ← index.php
│       ├── order/          ← checkout.php, list.php, detail.php
│       ├── admin/          ← dashboard, menu, orders, promo
│       └── layouts/        ← header.php, footer.php, admin_*.php
├── config/
│   ├── app.php             ← Konstanta, helper functions
│   ├── database.php        ← Koneksi MySQL + query helpers
│   └── schema.sql          ← DDL + seed data
├── public/
│   ├── index.php           ← Front controller (entry point)
│   ├── css/app.css         ← Main stylesheet (dark cosmic theme)
│   ├── css/admin.css       ← Admin panel styles
│   ├── js/app.js           ← Frontend JS (cart, modal, toast)
│   ├── js/admin.js         ← Admin JS (status update, polling)
│   ├── img/                ← Gambar statis
│   └── uploads/menu/       ← Upload gambar menu (writable!)
├── routes/
│   └── web.php             ← Router URL → Controller
├── .htaccess               ← Redirect semua ke public/index.php
└── README.md
```

---

## ⚡ Cara Setup (XAMPP)

### 1. Letakkan Project
```
Salin folder ke: C:/xampp/htdocs/luminosity-noodles/
```

### 2. Import Database
- Buka: http://localhost/phpmyadmin
- Klik tab **Import** → pilih `config/schema.sql` → klik **Go**

### 3. Konfigurasi DB
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // kosong = default XAMPP
define('DB_NAME', 'luminosity_noodles');
```

### 4. Konfigurasi APP_URL
Edit `config/app.php`:
```php
define('APP_URL', 'http://localhost/luminosity-noodles/public');
```

### 5. Aktifkan mod_rewrite
- Buka: `C:/xampp/apache/conf/httpd.conf`
- Cari: `#LoadModule rewrite_module modules/mod_rewrite.so`
- Hapus tanda `#` di depannya
- Cari: `AllowOverride None` (bagian `<Directory "C:/xampp/htdocs">`)
- Ganti jadi: `AllowOverride All`
- Restart Apache di XAMPP Control Panel

### 6. Izin Folder Upload
Pastikan folder `public/uploads/menu/` bisa ditulis (writable). Di Windows biasanya sudah otomatis.

---

## 🌐 URL Akses

| Halaman       | URL                                                        |
|---------------|------------------------------------------------------------|
| Home          | http://localhost/luminosity-noodles/public/                |
| Menu          | http://localhost/luminosity-noodles/public/menu            |
| Login         | http://localhost/luminosity-noodles/public/login           |
| Register      | http://localhost/luminosity-noodles/public/register        |
| Cart          | http://localhost/luminosity-noodles/public/cart            |
| Pesanan       | http://localhost/luminosity-noodles/public/orders          |
| Admin         | http://localhost/luminosity-noodles/public/admin           |

---

## 🔐 Akun Default

| Role  | Email                   | Password  |
|-------|-------------------------|-----------|
| Admin | admin@luminosity.com    | password  |

> ⚠️ **GANTI PASSWORD ADMIN** setelah pertama login!
> Lewat phpMyAdmin: UPDATE users SET password = '$2y$10$...' WHERE email = 'admin@luminosity.com'
> Atau tambah route ganti password di AdminController.

---

## 🔌 REST API Endpoints

| Method | Endpoint              | Deskripsi              | Auth     |
|--------|-----------------------|------------------------|----------|
| GET    | /api/menu             | Semua menu             | Public   |
| GET    | /api/cart             | Isi cart               | Login    |
| POST   | /api/cart             | Tambah ke cart         | Login    |
| DELETE | /api/cart             | Hapus dari cart        | Login    |
| POST   | /api/promo            | Validasi kode promo    | Login    |
| POST   | /api/checkout         | Buat pesanan           | Login    |
| GET    | /api/orders           | Pesanan user           | Login    |
| PUT    | /api/orders/{id}      | Update status pesanan  | Admin    |
| GET    | /api/stats            | Statistik penjualan    | Admin    |
| GET    | /api/poll-orders      | Live polling pesanan   | Admin    |

---

## 🛡️ Security Features

- ✅ **SQL Injection**: Semua query pakai Prepared Statements (MySQLi)
- ✅ **XSS Protection**: `htmlspecialchars()` via `sanitize()` di semua output
- ✅ **CSRF Protection**: Token di semua form POST & API call
- ✅ **Password Hashing**: `password_hash()` dengan `PASSWORD_BCRYPT`
- ✅ **Session Security**: `session_regenerate_id(true)` setelah login
- ✅ **File Upload Validation**: Cek ekstensi & ukuran file
- ✅ **Role-based Access**: Admin route wajib `isAdmin()`

---

## 🚀 Deploy ke VPS/Hosting

### Apache
```bash
# Pastikan mod_rewrite aktif
sudo a2enmod rewrite
sudo systemctl restart apache2

# Set document root ke /public atau gunakan .htaccess di root
```

### Nginx (ubah APP_URL & tambah config)
```nginx
location / {
    try_files $uri $uri/ /public/index.php?url=$uri&$query_string;
}
```

### Environment Production
Di `config/app.php`:
```php
define('APP_ENV', 'production');
define('APP_URL', 'https://luminositynoodles.com/public');
```

---

## 🎨 Kustomisasi Tema

Edit `public/css/app.css`, bagian `:root`:
```css
--accent-purple: #a855f7;   /* Warna utama neon */
--accent-blue:   #3b82f6;   /* Warna aksen biru */
--bg-base:       #060610;   /* Background utama */
```

---

Made with 💜 by Luminosity Team
