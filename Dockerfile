#FROM php:7.0.33-fpm-alpine
FROM php:7.4.33-alpine3.15
COPY /dist /var/www/booking.agrad.ru/
WORKDIR /var/www/booking.agrad.ru/
################################################
RUN apk add --no-cache \
tzdata \
fakeroot \
openssl \
libattr \
attr \
libacl \
tar \
pkgconf \
patch \
libgcc \
libstdc++ \
lzip \
ca-certificates \
brotli-libs \
nghttp2-libs \
libcurl \
curl \
abuild \
binutils \
libmagic \
file \
libgomp \
libatomic \
libgphobos \
gmp \
isl22 \
mpfr4 \
mpc1 \
gcc \
musl-dev \
libc-dev \
g++ \
make \
fortify-headers \
build-base \
expat \
pcre2 \
git \
alpine-sdk \
bash \
htop \
libressl-dev \
libxml2-dev \
logrotate \
mc \
memcached \
musl-locales \
nano \
net-tools \
nginx \
unixodbc \
unixodbc-dev \
wget \
jq \
php7-apache2 \
php7-bcmath \
php7-bz2 \
php7-calendar \
php7-cgi \
php7-common \
php7-ctype \
php7-curl \
php7-dba \
php7-dev \
php7-embed \
php7-enchant \
php7-fpm \
php7-gd \
php7-gmp \
php7-iconv \
php7-imap \
php7-intl \
php7-json \
php7-ldap \
php7-mbstring \
php7-mysqli \
php7-odbc \
php7-opcache \
php7-openssl \
php7-pdo_dblib \
php7-pear \
php7-pgsql \
php7-phar \
php7-phpdbg \
php7-pspell \
php7-simplexml \
php7-snmp \
php7-soap \
php7-sockets \
php7-sqlite3 \
php7-tidy \
php7-tokenizer \
php7-xml \
php7-xmlrpc \
php7-xmlwriter \
php7-xsl \
php7-zip \
php7-exif \
php7-fileinfo \
php7-ftp \
php7-gettext \
php7-pcntl \
php7-pdo_mysql \
php7-pdo_pgsql \
php7-pdo_sqlite \
php7-posix \
php7-shmop \
php7-sysvmsg \
php7-sysvsem \
php7-sysvshm \
php7-xmlreader \
php7-pecl-igbinary \
php7-pecl-imagick \
php7-pecl-memcache \
php7-pecl-memcached \
php7-pecl-redis \
php7-pecl-xdebug \
php7-pecl-apcu \
php7-pecl-mcrypt \
php7-pecl-msgpack \
php7-pecl-xhprof \
nginx-mod-stream \
libreoffice
################################################
COPY run.sh /opt/run.sh
###nginx###
RUN mkdir -p /run/nginx
COPY nginx/booking.conf /etc/nginx/conf.d/
COPY nginx/nginx.conf /etc/nginx/
COPY nginx/stream.conf /etc/nginx/stream.d//
###cert###
COPY letsencrypt/options-ssl-nginx.conf /etc/letsencrypt/
COPY letsencrypt/live/booking.agrad.ru/fullchain.pem /etc/letsencrypt/live/booking.agrad.ru/
COPY letsencrypt/live/booking.agrad.ru/privkey.pem /etc/letsencrypt/live/booking.agrad.ru/
COPY letsencrypt/ssl-dhparams.pem /etc/letsencrypt/
COPY /letsencrypt/live/stream.agrad.ru/fullchain.pem /etc/letsencrypt/live/stream.agrad.ru/
COPY /letsencrypt/live/stream.agrad.ru/privkey.pem /etc/letsencrypt/live/stream.agrad.ru/
###chown###
RUN chown -R nginx /etc/nginx/conf.d/booking.conf \
&& chown -R www-data:www-data /var/www/booking.agrad.ru \
&& chown -R nginx /etc/letsencrypt/live/booking.agrad.ru/privkey.pem \
&& chown -R nginx /etc/letsencrypt/options-ssl-nginx.conf \
&& chown -R nginx /etc/letsencrypt/live/booking.agrad.ru/fullchain.pem \
&& chown -R nginx /etc/letsencrypt/ssl-dhparams.pem

ENTRYPOINT /bin/bash /opt/run.sh && /bin/bash