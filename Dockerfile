FROM laravelsail/php83-composer:latest

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /app

# Copy only the necessary files for composer install
COPY composer.json composer.lock* /app/

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --no-interaction --no-dev

# Copy only the necessary files for npm install
COPY package*.json /app/
COPY vite.config.js /app/

# Install npm dependencies with clean cache
ENV DOCKER_BUILD=true
RUN npm cache clean --force && \
    npm install --legacy-peer-deps --no-fund --no-audit

# Copy the rest of the application
COPY . .

# Generate application key and optimize
RUN php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Build assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data \
    /app/storage \
    /app/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
