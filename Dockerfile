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

# Copy package files first for better layer caching
COPY package*.json ./

# Install npm dependencies
RUN npm install

# Copy application files
COPY . .

# Set working directory
WORKDIR /app

# Generate application key
RUN php artisan key:generate

# Set up storage and cache permissions
RUN chown -R www-data:www-data \
    /app/storage \
    /app/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
