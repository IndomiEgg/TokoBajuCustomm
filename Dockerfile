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
CMD ["sh", "-c", "php spark migrate 2>&1 || true && exec php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
