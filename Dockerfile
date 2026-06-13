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

# Generate .env from environment variables and run migrations/server
# Use PHP because shell variable names cannot contain dots.
CMD ["sh", "-c", "\
    php -r ' \ 
        $map = [ \ 
            "CI_ENVIRONMENT" => ["CI_ENVIRONMENT", "CI_ENV"], \ 
            "app.baseURL" => ["app.baseURL", "APP_BASEURL", "APP_BASE_URL", "BASEURL", "BASE_URL"], \ 
            "database.default.hostname" => ["database.default.hostname", "DATABASE_HOST", "MYSQLHOST", "DB_HOST", "DATABASE_HOST"], \ 
            "database.default.port" => ["database.default.port", "DATABASE_PORT", "MYSQLPORT", "DB_PORT"], \ 
            "database.default.database" => ["database.default.database", "DATABASE_NAME", "DATABASE", "MYSQLDATABASE", "DB_DATABASE"], \ 
            "database.default.username" => ["database.default.username", "DATABASE_USER", "DATABASE_USERNAME", "MYSQLUSER", "DB_USER"], \ 
            "database.default.password" => ["database.default.password", "DATABASE_PASSWORD", "MYSQLPASSWORD", "DB_PASSWORD", "DB_PASS"], \ 
            "database.default.DBDriver" => ["database.default.DBDriver", "DATABASE_DRIVER", "DB_DRIVER"], \ 
        ]; \ 
        file_put_contents("/app/.env", ""); \ 
        foreach ($map as $key => $names) { \ 
            $value = null; \ 
            foreach ($names as $name) { \ 
                $val = getenv($name); \ 
                if ($val !== false) { \ 
                    $value = $val; \ 
                    break; \ 
                } \ 
            } \ 
            file_put_contents("/app/.env", "$key=" . ($value === null ? '' : $value) . "\n", FILE_APPEND); \ 
        } \ 
    ' && \
    echo '.env generated:' && cat /app/.env && \
    echo 'Running migrations...' && \
    php spark migrate 2>&1 || true && \
    echo 'Starting server...' && \
    exec php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
