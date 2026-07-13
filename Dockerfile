FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    vim \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite headers

# Ensure only one MPM is enabled (avoid "More than one MPM loaded").
# Prefer `mpm_prefork` which is compatible with mod_php provided by the
# `php:*-apache` base image. Disable other MPMs if present.
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork || true

# Ensure mpm_prefork is the only MPM enabled
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true \
    && a2enmod mpm_prefork

# Create log directory and set permissions
RUN mkdir -p /var/log/php \
    && chown -R www-data:www-data /var/log/php \
    && chmod -R 755 /var/log/php

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration (if exists)
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Create necessary directories
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create optimized entrypoint script
RUN cat > /usr/local/bin/docker-entrypoint.sh <<'EOF'
#!/bin/bash
set -e

# Quick permission fix for uploads only (volume-mounted)
chown -R www-data:www-data /var/www/html/uploads 2>/dev/null || true

# Start Apache immediately (MPM already configured during build)
exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh || true

# Configure PHP
RUN echo "upload_max_filesize = 50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
