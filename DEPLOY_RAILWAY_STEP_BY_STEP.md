# BATOM — TokoBajuCustom | Railway Deployment Checklist

## ✅ Persiapan (5 menit)

### 1. Bersihkan & Commit Kode Lokal

```bash
# Pastikan sudah di folder project
cd TokoBajuCustom

# Cek status file
git status

# Tambahkan semua file (temporary files sudah dihapus)
git add .

# Commit
git commit -m "Prepare for Railway deployment"
```

### 2. Buat GitHub Repository (jika belum ada)

- Buka https://github.com/new
- Nama: `TokoBajuCustom`
- Pilih: Public (supaya Railway bisa akses)
- Jangan initialize README (kita sudah punya)
- Create repository

### 3. Push Kode ke GitHub

```bash
git remote add origin https://github.com/YOUR_USERNAME/TokoBajuCustom.git
git branch -M main
git push -u origin main
```

---

## ✅ Setup di Railway (10 menit)

### 4. Create Railway Account

- Buka https://railway.app
- Sign up dengan GitHub (recommended)
- Authorize Railway

### 5. Create Project & Database

**Step A: Create Project**

1. Klik "Create New Project"
2. Pilih "Provision MySQL"
   - Ini akan membuat database MySQL yang siap pakai
3. Tunggu hingga selesai (1-2 menit)

**Step B: Catat Credentials**

- Buka tab MySQL → Connect
- Copy credentials:
  - **Host**: `xxx.railway.internal`
  - **Port**: `3306`
  - **Database**: `railway` (atau yang lain)
  - **User**: `root`
  - **Password**: (copy dari sini)

### 6. Add GitHub Repository ke Project

1. Buka project → Deployments tab
2. Klik "Connect Repository"
3. Authorize Railway untuk akses GitHub
4. Pilih `TokoBajuCustom` repository
5. Klik "Deploy"

---

## ✅ Konfigurasi Environment (5 menit)

### 7. Set Environment Variables

1. Di Railway Project → Variables tab
2. Klik "Add Variable"
3. Tambahkan setiap variable satu per satu:

```
CI_ENVIRONMENT: production
database.default.hostname: [RAILWAY_MYSQL_HOST dari langkah 5]
database.default.database: railway
database.default.username: root
database.default.password: [PASSWORD dari langkah 5]
database.default.port: 3306
```

**Contoh:**

```
database.default.hostname: ctnr-gp5k9d6-mysql.railway.internal
database.default.password: PaSsW0rd123
```

### 8. Rebuild Deploy

1. Klik "Deployments" tab
2. Lihat deploy terakhir
3. Klik tombol "Redeploy" (untuk apply variable baru)
4. Tunggu hingga "SUCCESS" (5-10 menit)

---

## ✅ Database Setup (2 menit)

### 9. Run Migrations

1. Di Railway → Console tab
2. Jalankan command:

```
php spark migrate --all
```

3. Atau jika perlu custom seed:

```
php spark db:seed --namespace App\\Database\\Seeds
```

Tunggu hingga "Migration complete" ✅

---

## ✅ Verifikasi Deployment (2 menit)

### 10. Test Website

1. Buka Railway Project → Environment tab
2. Cari "Deployment URL" (misal: `https://batom-custom-prod.railway.app`)
3. Klik atau copy URL

### 11. Test Endpoints

- **Homepage**: `https://batom-custom-prod.railway.app/`
  - Harus muncul landing page dengan gallery
- **Admin Login**: `https://batom-custom-prod.railway.app/admin/login`
  - Harus muncul login form
- **User Login**: `https://batom-custom-prod.railway.app/login`
  - Harus muncul auth page

Jika semua berjalan → **SUKSES! 🎉**

---

## ⚠️ Troubleshooting

### Problem: Deployment Failed (Error di Railway)

**Solusi:**

1. Klik deployment → View Logs
2. Cari error message
3. Kalau masalah DB:
   - Double-check credentials
   - Pastikan host/port correct
4. Redeploy lagi

### Problem: Website Muncul White Screen

**Solusi:**

1. Check .env variables sudah benar
2. Check file permissions di Railway (writable folder)
3. Lihat error log di Railway Console

### Problem: Database Connection Error

**Solusi:**

1. Verify DATABASE URL di Railway Variables
2. Jalankan test connection:
   ```
   php spark db:table users --table
   ```
3. Jika error, check credentials ulang

### Problem: Assets (CSS/JS) tidak muncul

**Solusi:**

1. Verifikasi DocumentRoot di Dockerfile
2. Check public/.htaccess
3. Verify permissions

---

## 📝 Quick Reference

| Komponent       | Status                 |
| --------------- | ---------------------- |
| Code Repository | ✅ GitHub              |
| Database        | ✅ Railway MySQL       |
| Environment     | ✅ Set di Railway      |
| Docker          | ✅ Dockerfile included |
| Migrations      | ✅ Siap di-run         |
| Deployment      | ✅ Auto-deploy on push |

---

## 🚀 Untuk Update Ke Depan

Setelah deployment selesai, workflow jadi sederhana:

```bash
# Edit code lokal
# Test di localhost

# Commit & push
git add .
git commit -m "Update features"
git push origin main

# Railway auto-deploy dalam 2-3 menit
# Cek status di Railway Dashboard
```

---

## 📞 Need Help?

- Railway Docs: https://docs.railway.app
- CodeIgniter 4 Docs: https://codeigniter.com/user_guide/4/index.html
- Check Railway Project Logs untuk error details
