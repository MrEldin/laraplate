FROM php:7.3-fpm

# Install "curl", "libmemcached-dev", "libpq-dev", "libjpeg-dev",
#         "libpng12-dev", "libfreetype6-dev", "libssl-dev", "libmcrypt-dev",
RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libzip-dev \
    git \
    unzip \
    zip \
    ssh-client \
  && pecl install mcrypt-1.0.2 \
  && DEBIAN_FRONTEND=noninteractive apt-get install -y build-essential xorg libssl-dev libxrender-dev wget gdebi tar \
  && wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && tar vxf wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && cp wkhtmltox/bin/wk* /usr/local/bin/ \
  && rm -rf /var/lib/apt/lists/*

# Install the PHP mcrypt extention
RUN docker-php-ext-enable mcrypt \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  # Install the PHP pdo_mysql extention
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-install pcntl \
  && docker-php-ext-install bcmath \
  && docker-php-ext-install exif \
  # Install the PHP pdo_pgsql extention
  && docker-php-ext-install pdo_pgsql \
  # Install the PHP gd library
  && docker-php-ext-configure gd \
    --with-jpeg-dir=/usr/lib \
    --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd \
  && docker-php-ext-configure zip \
     --with-libzip=/usr/include \
  && docker-php-ext-configure zip --with-libzip=/usr/include \
  && docker-php-ext-install zip


#RUN pecl install xdebug-2.5.0 \
#    docker-php-ext-enable  xdebug
#
RUN pecl install mongodb \
    docker-php-ext-enable mongodb

EXPOSE 6001
