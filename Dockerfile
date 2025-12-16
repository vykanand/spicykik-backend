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

# Create log directory and set permissions
RUN mkdir -p /var/log/php \
    && chown -R www-data:www-data /var/log/php \
    && chmod -R 755 /var/log/php

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html

# Create robust entrypoint script to sanitize Apache MPMs and start Apache
RUN cat > /usr/local/bin/docker-entrypoint.sh <<'EOF'
#!/bin/bash
set -e

# Make sure web dir permissions are sane
chown -R www-data:www-data /var/www/html || true
chmod -R 755 /var/www/html || true

# Comment out any explicit LoadModule lines that load MPM modules from custom configs.
# This prevents configuration files copied from other systems loading a second MPM.
grep -RIl "LoadModule[[:space:]]\+mpm_" /etc/apache2 || true
for f in $(grep -RIl "LoadModule[[:space:]]\+mpm_" /etc/apache2 || true); do
    sed -i -E "s/^[[:space:]]*(LoadModule[[:space:]]+mpm_[^ ]+)/# \1/" "$f" || true
done

# Remove any enabled mpm symlinks that may persist from previous images/volumes
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true

# Disable common MPMs and explicitly enable the prefork MPM (required for mod_php)
a2dismod mpm_event mpm_worker || true
a2enmod mpm_prefork || true

# Start Apache in foreground
exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure PHP
RUN echo "upload_max_filesize = 50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
