FROM php:8.2-cli

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    default-mysql-client \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql mysqli intl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Do not generate a .env from .env.example in production.
# Railway should provide configuration via environment variables.

# Create writable directories and set permissions
RUN mkdir -p writable/logs writable/cache writable/session writable/uploads && \
    chmod -R 777 writable

EXPOSE 8080

# Run migrations (allow failure) and start server
# Generate .env file from Railway environment variables on startup
CMD ["sh", "-c", "\
    echo 'CI_ENVIRONMENT=${CI_ENVIRONMENT:-production}' > /app/.env && \
    echo 'app.baseURL=${APP_BASEURL:-https://tokobajucustomm-production.up.railway.app/}' >> /app/.env && \
    echo 'database.default.hostname=${DATABASE_HOST:-mysql.railway.internal}' >> /app/.env && \
    echo 'database.default.port=${DATABASE_PORT:-3306}' >> /app/.env && \
    echo 'database.default.database=${DATABASE_NAME:-railway}' >> /app/.env && \
    echo 'database.default.username=${DATABASE_USER:-root}' >> /app/.env && \
    echo 'database.default.password=${DATABASE_PASSWORD:-}' >> /app/.env && \
    echo 'database.default.DBDriver=MySQLi' >> /app/.env && \
    php spark migrate 2>&1 || true && \
    exec php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
