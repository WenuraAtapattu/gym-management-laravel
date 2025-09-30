# Use PHP 8.3 with Composer as base image
FROM laravelsail/php83-composer:latest

# Set environment variables
ENV NODE_VERSION=18.17.1
ENV NVM_DIR=/root/.nvm
ENV PATH="${NVM_DIR}/versions/node/v${NODE_VERSION}/bin:${PATH}"

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

# Install NVM and Node.js
RUN mkdir -p ${NVM_DIR} \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash \
    && . "$NVM_DIR/nvm.sh" \
    && nvm install ${NODE_VERSION} \
    && nvm use v${NODE_VERSION} \
    && nvm alias default v${NODE_VERSION} \
    && npm install -g npm@latest

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json /app/
COPY vite.config.js /app/

# Install npm dependencies
RUN npm install --legacy-peer-deps --no-fund --no-audit

# Copy composer files
COPY composer.json composer.lock /app/

# Install PHP dependencies without scripts
RUN composer install --no-interaction --no-scripts --no-dev --no-autoloader

# Copy the rest of the application
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data \
    /app/storage \
    /app/bootstrap/cache \
    /app/public/build

# Install PHP dependencies with optimization
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Generate .env if it doesn't exist
RUN if [ ! -f .env ]; then \
        cp .env.example .env; \
    fi

# Generate application key if not set
RUN grep -q '^APP_KEY=$' .env && php artisan key:generate --no-interaction || true

# Build assets
RUN npm run build

# Set proper permissions after build
RUN chown -R www-data:www-data /app/public/build

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan view:cache

# Set working directory to public
WORKDIR /app/public

# Expose port 8000
EXPOSE 8000

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
