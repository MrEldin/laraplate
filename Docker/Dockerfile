FROM php:7.1-fpm

# Install "curl", "libmemcached-dev", "libpq-dev", "libjpeg-dev",
#         "libpng12-dev", "libfreetype6-dev", "libssl-dev", "libmcrypt-dev",
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    git \
    unzip \
  && apt-get install -y build-essential xorg libssl-dev libxrender-dev wget gdebi tar \
  && wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && tar vxf wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && cp wkhtmltox/bin/wk* /usr/local/bin/ \
  && rm -rf /var/lib/apt/lists/*

# Install the PHP mcrypt extention
RUN docker-php-ext-install mcrypt \
  # Install the PHP pdo_mysql extention
  && docker-php-ext-install pdo_mysql \
  # Install the PHP pdo_pgsql extention
  && docker-php-ext-install pdo_pgsql \
  # Install the PHP gd library
  && docker-php-ext-configure gd \
    --enable-gd-native-ttf \
    --with-jpeg-dir=/usr/lib \
    --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd


#RUN pecl install xdebug-2.5.0 \
#    docker-php-ext-enable  xdebug
#
RUN pecl install mongodb \
    docker-php-ext-enable mongodb
