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

# Only sanitize custom/conf files that may have been copied from other systems.
# Avoid modifying Debian-provided mods-available files.
for dir in /etc/apache2/conf-enabled /etc/apache2/sites-enabled /etc/apache2/conf-available /etc/apache2/sites-available /etc/apache2/conf.d; do
    if [ -d "$dir" ]; then
        for f in $(grep -RIl "LoadModule[[:space:]]\+mpm_" "$dir" 2>/dev/null || true); do
            sed -i -E "s/^[[:space:]]*(LoadModule[[:space:]]+mpm_[^ ]+)/# \1/" "$f" || true
        done
    fi
done

# Ensure the system's prefork mod file is present and correct (restore if missing/modified)
cat > /etc/apache2/mods-available/mpm_prefork.load <<'MPM'
LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so
MPM

# Clean any enabled mpm symlinks (we'll enable the correct one below)
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true

# Disable other MPMs if enabled and enable prefork
a2dismod mpm_event mpm_worker || true
a2enmod mpm_prefork || true

# Start Apache in foreground
exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh || true

# Configure PHP
RUN echo "upload_max_filesize = 50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
