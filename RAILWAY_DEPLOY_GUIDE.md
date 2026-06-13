# Railway Deployment Guide untuk TokoBajuCustom

## Prerequisite

- Buat akun di [railway.app](https://railway.app)
- Install Railway CLI: `npm install -g @railway/cli`
- Atau gunakan Railway UI di web browser

## Step 1: Persiapan Database di Railway

1. Login ke Railway.app
2. Create New Project → Provision MySQL Database
3. Copy connection credentials (copy seluruh DATABASE_URL atau pisahkan components)

## Step 2: Configure Environment Variables

Di Railway Project Settings → Variables, tambahkan:

```
CI_ENVIRONMENT=production
database.default.hostname=<RAILWAY_MYSQL_HOST>
database.default.database=<RAILWAY_MYSQL_DATABASE>
database.default.username=<RAILWAY_MYSQL_USER>
database.default.password=<RAILWAY_MYSQL_PASSWORD>
database.default.port=3306
```

Atau jika Railway menyediakan DATABASE_URL format:

```
DATABASE_URL=mysql://<user>:<pass>@<host>:<port>/<database>
```

Update app/Config/Database.php untuk membaca dari environment:

```php
public array $default = [
    'DSN'      => $_ENV['DATABASE_URL'] ?? '',
    'hostname' => $_ENV['database.default.hostname'] ?? 'localhost',
    'username' => $_ENV['database.default.username'] ?? 'root',
    'password' => $_ENV['database.default.password'] ?? '',
    'database' => $_ENV['database.default.database'] ?? 'batom_custom',
    'port'     => $_ENV['database.default.port'] ?? 3306,
    // ... rest config
];
```
> Important: In production, do not generate `.env` from `.env.example` during build. If a `.env` file is created with placeholder values, it may override Railway environment variables and break the deployment.
## Step 3: Push to Git Repository

```bash
git init
git add .
git commit -m "Initial deployment setup"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/TokoBajuCustom.git
git push -u origin main
```

## Step 4: Deploy via Railway

### Option A: Via CLI

```bash
railway login
railway init  # Select your GitHub repo
railway up
```

### Option B: Via Web UI

1. Go to Railway.app → Dashboard
2. Create New Project → Deploy from GitHub
3. Select your TokoBajuCustom repository
4. Railway akan auto-detect Dockerfile dan deploy

## Step 5: Run Database Migrations

After first deploy:

```bash
railway run php spark migrate --all
```

Atau di Railway UI → Console:

```
php spark migrate --all
```

## Step 6: Verify Deployment

1. Railway akan provide URL (misal: `https://projectname.railway.app`)
2. Test:
   - Cek homepage: `https://projectname.railway.app/`
   - Cek admin login: `https://projectname.railway.app/admin/login`
   - Test API: `https://projectname.railway.app/api/endpoint`

## Troubleshooting

### Issue: Migration failed

- Check logs: Railway Dashboard → Deployments → View Logs
- Verify DATABASE_URL dan credentials
- Run manually: `railway run php spark migrate --all`

### Issue: White screen / 500 error

- Check Logs di Railway
- Verify .env variables sudah di-set
- Check writable/ folder permissions

### Issue: Assets tidak load

- Verify .htaccess di public folder
- Check DocumentRoot di Dockerfile

## Advanced: Custom Domain

1. Railway Project Settings → Custom Domain
2. Point DNS ke Railway nameservers
3. Selesai

## Automatic Deployments

Railway auto-deploy setiap push ke `main` branch. Untuk disable:

- Project Settings → Deployments → Disable Auto Deploy (jika perlu manual control)

---

**Helpful Links:**

- [Railway Documentation](https://docs.railway.app)
- [CodeIgniter 4 Deployment](https://codeigniter.com/user_guide/general/requirements.html#production-deployment)
