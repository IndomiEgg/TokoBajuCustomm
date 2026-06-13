# Railway Environment Variables Setup

## Required Variables di Railway Dashboard

Go to Railway Dashboard → TokoBajuCustomm → Settings → Variables

Set these EXACT names and values:

```
CI_ENVIRONMENT = production

DATABASE_HOST = mysql.railway.internal
DATABASE_PORT = 3306
DATABASE_NAME = railway
DATABASE_USER = root
DATABASE_PASSWORD = PVIdnNAYuQUQMbLHZbIQCovQwdPGMNRp

APP_BASEURL = https://tokobajucustomm-production.up.railway.app/
```

## PENTING: Variable Names Format

Railway supports **UPPERCASE_SNAKE_CASE** format (not dot-notation like `database.default.hostname`).

This Dockerfile generates `/app/.env` file from these variables at startup, which CodeIgniter reads.

## Verification

After setting variables in Railway, redeploy:
1. Go to TokoBajuCustomm service
2. Click "Redeploy"
3. Wait for deployment to complete
4. Check Deploy Logs for database connection errors

Expected log (no error):
```
[CodeIgniter\\Migrations\\MigrationRunner] Batch 0 (...)
```

If still see "Unable to connect to the database", verify:
1. All 7 variables above are set in Railway
2. No typos in variable names
3. Try redeploy again
4. Check MySQL service is running (green status)

## Local Testing

To test locally before Railway deploy:
```bash
export DATABASE_HOST=mysql.railway.internal
export DATABASE_PORT=3306
export DATABASE_NAME=railway
export DATABASE_USER=root
export DATABASE_PASSWORD=PVIdnNAYuQUQMbLHZbIQCovQwdPGMNRp
export CI_ENVIRONMENT=production
export APP_BASEURL=http://localhost:8080/

php spark migrate
php -S 127.0.0.1:8080 -t public public/index.php
```
