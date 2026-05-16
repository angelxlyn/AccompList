# ── Base image: PHP 8.3 with Apache ──────────────────────────────────────────
FROM php:8.3-apache

# ── System dependencies ───────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    gnupg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Node.js 20 (for Vite build) ───────────────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# ── Apache: enable mod_rewrite + point document root to /public ───────────────
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# ── Render uses a dynamic $PORT — configure Apache to listen on it ────────────
# We do this at runtime in the start script below.

# ── Working directory ─────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── Copy application code ─────────────────────────────────────────────────────
COPY . .

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# ── NPM / Vite build ──────────────────────────────────────────────────────────
RUN npm install && npm run build

# ── Laravel file permissions ──────────────────────────────────────────────────
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ── Startup script ────────────────────────────────────────────────────────────
# Render injects $PORT at runtime. Apache must listen on that port.
# We also run migrations here so the DB is always up to date on deploy.
COPY docker-start.sh /usr/local/bin/docker-start.sh
RUN chmod +x /usr/local/bin/docker-start.sh

EXPOSE 80

CMD ["/usr/local/bin/docker-start.sh"]