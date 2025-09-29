# 🚀 **PANDUAN HOSTING WEBSITE IPM TARA**

## 📋 **File yang Diperlukan untuk Hosting**

### **🎯 File Utama (WAJIB)**
- `index.html` - Halaman utama website
- `styles.css` - Styling website
- `script.js` - JavaScript untuk interaksi
- `IPM.png` - Logo IPM

### **📄 Halaman Tambahan**
- `sejarah-ipm.html` - Halaman sejarah IPM
- `struktur-pd.html` - Halaman struktur PD IPM Sragen

### **🗄️ Database & Backend (OPSIONAL)**
- `database_schema.sql` - Skema database MySQL
- `config_database.php` - Konfigurasi koneksi database
- `api_endpoints.php` - API endpoints untuk backend
- `setup_database.php` - Script setup database
- `test_database.php` - Script testing database

### **⚙️ Konfigurasi**
- `.htaccess` - Konfigurasi Apache
- `robots.txt` - Direktif untuk search engine
- `sitemap.xml` - Peta situs
- `manifest.json` - PWA manifest

## 🚀 **Cara Upload ke Hosting**

### **1. Hosting Static (Tanpa Database)**
Jika hanya ingin website statis:
```
1. Upload semua file ke folder public_html
2. Pastikan index.html ada di root folder
3. Website siap digunakan!
```

### **2. Hosting dengan Database**
Jika ingin fitur registrasi dan login:
```
1. Upload semua file ke folder public_html
2. Buat database MySQL di hosting
3. Import database_schema.sql ke database
4. Edit config_database.php dengan info database hosting
5. Website siap digunakan!
```

## 📁 **Struktur Folder Hosting**

```
hosting/
├── index.html              # Halaman utama
├── styles.css              # Styling
├── script.js               # JavaScript
├── IPM.png                 # Logo
├── sejarah-ipm.html        # Halaman sejarah
├── struktur-pd.html        # Halaman struktur
├── database_schema.sql     # Skema database
├── config_database.php     # Konfigurasi database
├── api_endpoints.php       # API endpoints
├── setup_database.php      # Setup database
├── test_database.php       # Test database
├── .htaccess               # Konfigurasi Apache
├── robots.txt              # SEO
├── sitemap.xml             # Sitemap
├── manifest.json           # PWA
└── README_HOSTING.md       # Panduan ini
```

## ⚡ **Fitur Website**

### **✅ Fitur yang Sudah Selesai**
- ✅ Halaman utama dengan hero section
- ✅ Navigasi dengan dropdown Profil dan Direktori
- ✅ Halaman Sejarah IPM
- ✅ Halaman Struktur PD IPM Sragen
- ✅ Direktori 7 buku panduan IPM
- ✅ Modal login dan registrasi
- ✅ Form registrasi lengkap dengan dropdown
- ✅ Responsive design
- ✅ Animasi dan efek visual

### **🔧 Fitur Backend (Opsional)**
- 🔧 Sistem registrasi anggota
- 🔧 Sistem login
- 🔧 Database MySQL
- 🔧 API endpoints
- 🔧 Upload foto profil

## 🌐 **Hosting yang Direkomendasikan**

### **Hosting Gratis**
- **Netlify** - Untuk website statis
- **Vercel** - Untuk website statis
- **GitHub Pages** - Untuk website statis

### **Hosting Berbayar**
- **Hostinger** - Murah dan mudah
- **Niagahoster** - Lokal Indonesia
- **DigitalOcean** - Untuk developer

## 📞 **Support**

Jika ada masalah dengan hosting:
1. Cek file .htaccess
2. Pastikan semua file terupload
3. Cek konfigurasi database
4. Lihat error log di hosting

**Website IPM TARA siap dihosting! 🎉**
