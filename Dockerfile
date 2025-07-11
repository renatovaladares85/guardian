# ===============================================================================
# GUARDIAN DOCKER IMAGE
# © 2025 Renato Valadares - Todos os direitos reservados
# Sistema de Gestão Empresarial - Laravel 11 + PHP 8.2
# ===============================================================================

FROM php:8.2-apache

# Metadata
LABEL maintainer="renatovaladares85@gmail.com"
LABEL version="1.0.0"
LABEL description="Guardian - Sistema de Gestão Empresarial"
LABEL copyright="© 2025 Renato Valadares"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers ssl

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application with proper ownership
COPY --chown=www-data:www-data . /var/www/html

# Set permissions
RUN chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health-check || exit 1

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
