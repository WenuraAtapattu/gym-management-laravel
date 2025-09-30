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

# Copy only composer files first
COPY composer.json composer.lock /app/

# Install PHP dependencies without scripts
RUN composer install --no-interaction --no-scripts --no-dev --no-autoloader

# Copy the rest of the application except node_modules and other unnecessary files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data \
    /app/storage \
    /app/bootstrap/cache

# Install dependencies with optimization (no scripts)
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Copy .env if it doesn't exist
RUN if [ ! -f .env ]; then \
        cp .env.example .env; \
    fi

# Generate application key if not set
RUN grep -q '^APP_KEY=$' .env && php artisan key:generate --no-interaction || true

# Cache configuration
RUN php artisan config:cache

# Cache views if the views directory exists
RUN if [ -d "resources/views" ]; then \
        php artisan view:cache; \
    fi

# Install npm dependencies and build assets in one layer to reduce image size
RUN npm cache clean --force && \
    npm install --legacy-peer-deps --no-fund --no-audit && \
    npm run build

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port 8000
EXPOSE 8000

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
