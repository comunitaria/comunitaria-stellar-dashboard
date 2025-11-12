FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libgmp-dev \
    zip \
    unzip \
    cron \
    mariadb-client \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd intl gmp \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (this layer will be cached if composer files don't change)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy application files
COPY . /var/www/html

# Complete composer installation with scripts and autoloader
RUN composer dump-autoload --no-dev --optimize

# Set permissions - ensure www-data owns everything and writable is writable
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/writable \
    && chown -R www-data:www-data /var/www/html/writable

# Configure Apache document root to public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy Apache vhost configuration
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy permission fix script
COPY docker/fix-permissions.sh /usr/local/bin/fix-permissions.sh
RUN chmod +x /usr/local/bin/fix-permissions.sh

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Fix permissions on startup and start Apache
CMD ["/bin/sh", "-c", "/usr/local/bin/fix-permissions.sh && apache2-foreground"]
