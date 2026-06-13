#!/bin/sh
set -e

cat > /app/.env <<EOT
CI_ENVIRONMENT=${CI_ENVIRONMENT:-production}
app.baseURL=${APP_BASEURL:-https://tokobajucustomm-production.up.railway.app/}
database.default.hostname=${DATABASE_HOST:-mysql.railway.internal}
database.default.port=${DATABASE_PORT:-3306}
database.default.database=${DATABASE_NAME:-railway}
database.default.username=${DATABASE_USER:-root}
database.default.password=${DATABASE_PASSWORD:-${DB_PASSWORD:-${MYSQLPASSWORD:-}}}
database.default.DBDriver=MySQLi
EOT

echo ".env generated:"
cat /app/.env

echo "Running migrations..."
php spark migrate 2>&1 || true

echo "Starting server..."
exec php -S 0.0.0.0:${PORT:-8080} -t public public/index.php
