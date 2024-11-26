FROM php:7.4-apache

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    g++

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd xml zip intl

# Enable Apache modules
RUN a2enmod rewrite

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user


database.default.hostname=localhost
database.default.database=ci4
database.default.username=root
database.default.password=FzBReYoGP71eLMR4Vajh
database.default.DBDriver=MySQLi
database.default.DBPrefix=


# Copy the rest of the application files
COPY www/ /var/www/html/
COPY php-config/php.ini /usr/local/etc/php/conf.d/php.ini

# Ensure .env file is properly copied
COPY www/.env /var/www/html/.env

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Copy Apache configuration to set ServerName directive
COPY apache/apache-config.conf /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

# Run composer install
RUN composer install --prefer-dist --no-scripts --no-dev --optimize-autoloader

# Allow .htaccess files by configuring Apache
 # Correctly set Apache Directory configuration to allow .htaccess overrides
RUN echo '<Directory "/var/www/html/">' > /etc/apache2/conf-available/docker-apache-override.conf \
        && echo 'Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/docker-apache-override.conf \
        && echo 'AllowOverride All' >> /etc/apache2/conf-available/docker-apache-override.conf \
        && echo 'Require all granted' >> /etc/apache2/conf-available/docker-apache-override.conf \
        && echo '</Directory>' >> /etc/apache2/conf-available/docker-apache-override.conf \
        && a2enconf docker-apache-override

# Expose port 80 and start php-fpm server
EXPOSE 80
