FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user=sail
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Installer le package manquant
RUN npm install @inertiajs/progress

# Exécuter composer et npm en tant que root (pour éviter les problèmes de permission)
RUN composer install

# Installer les dépendances npm et construire les assets
RUN npm install && npm run build

# Corriger les permissions après le build
RUN chown -R ${user}:${user} /var/www/public

# Change current user to the created user
USER ${user}

EXPOSE 9000

CMD ["php-fpm"]
