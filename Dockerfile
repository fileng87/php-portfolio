# ---- Base Stage ----
# Common base for both production and devcontainer
FROM php:8.2-apache as base

# Install system dependencies and PHP extensions required for the application
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql

# Copy custom PHP configuration (optional)
# COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# ---- Composer Stage (Optional - if using Composer) ----
# FROM composer:lts as composer_stage
# WORKDIR /app
# COPY composer.json composer.lock* ./
# RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# ---- Production Stage ----
# Final, minimal stage for production deployment
FROM base as production

# Copy Composer dependencies from the composer stage (if used)
# COPY --from=composer_stage /app/vendor /var/www/html/vendor

# Copy application code into the web root
# Consider excluding files not needed in production (e.g., .git, .devcontainer) via .dockerignore
COPY . /var/www/html/

# Ensure the web server has the correct permissions
RUN chown -R www-data:www-data /var/www/html

# Apache is started by the base image

# ---- Dev Container Stage ----
# Stage specifically for the development container environment
FROM mcr.microsoft.com/devcontainers/php:1-8.2-bookworm as devcontainer

# Install system dependencies and PHP extensions required for the application
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql

# You could install additional build tools here if needed, 
# but it's often better to use Dev Container Features for this.

# Example: Create a non-root user for development (matching typical dev container setup)
# RUN groupadd --gid 1000 vscode \
#    && useradd --uid 1000 --gid 1000 -m -s /bin/bash vscode
# USER vscode

# Copy application code (dev environment might mount over this anyway)
COPY . /var/www/html/

# Optional dev-specific setup commands...

# Final CMD for devcontainer stage might be overridden or not strictly necessary
# if devcontainer.json specifies a command or sleep.

# Optional: Add healthcheck
# HEALTHCHECK --interval=5m --timeout=3s \
#  CMD curl -f http://localhost/ || exit 1 