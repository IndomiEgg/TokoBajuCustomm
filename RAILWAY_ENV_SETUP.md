# Railway Environment Variables Setup

## Required Environment Variables for Production

Untuk deployment di Railway, pastikan set environment variables berikut di Railway Dashboard:

### Database Configuration (WAJIB)
```
database.default.hostname=<railway-mysql-host>
database.default.username=<railway-mysql-user>
database.default.password=<railway-mysql-password>
database.default.database=batom_studio
database.default.port=3306
database.default.DBDriver=MySQLi
```

### Application Configuration
```
CI_ENVIRONMENT=production
app.baseURL=https://tokobajucustom-production.up.railway.app/
```

### Optional Security
```
app.forceGlobalSecureRequests=true
app.CSPEnabled=true
```

## How to Get Railway MySQL Credentials

1. Go to your Railway Project Dashboard
2. Open the MySQL service
3. Click "Connect" tab
4. Copy credentials from the connection string:
   - **Hostname**: The host part from connection string
   - **Username**: Usually `root` or the database user
   - **Password**: The password part
   - **Database**: Default `railway` or your custom database name

## Example Connection String
Railway biasanya menyediakan format:
```
mysql://<username>:<password>@<hostname>:<port>/<database>
```

## Setting Up in Railway Dashboard

1. Open your Railway project
2. Go to **Variables** tab
3. Add each variable one by one
4. Click **Save** after each addition
5. Trigger a redeploy

## Verification Checklist

- [ ] Database hostname is set correctly
- [ ] Database credentials are correct (test with a DB client first)
- [ ] Database name matches what you created
- [ ] CI_ENVIRONMENT is set to `production`
- [ ] app.baseURL is set to your Railway domain
- [ ] Redeploy after changing variables

## Troubleshooting

### If registration still fails:

1. **Check Database Connection**
   ```bash
   # In Railway terminal or SSH
   mysql -h <hostname> -u <username> -p<password> -D batom_studio -e "SHOW TABLES;"
   ```

2. **Check Application Logs**
   - Go to Railway Dashboard
   - Click your app service
   - Go to **Logs** tab
   - Look for error messages from `/writable/logs/`

3. **Verify Database Schema**
   ```bash
   mysql -h <hostname> -u <username> -p<password> -D batom_studio -e "DESC users;"
   ```

4. **Check if Migrations Ran**
   ```bash
   # In Railway terminal
   php spark migrate:latest
   ```

## Important Notes

- **Never commit .env with real credentials** - Only use Environment Variables in Railway
- **Database migrations** should run automatically on deployment
- **Multiple environments**: Different sets of variables for staging vs production
