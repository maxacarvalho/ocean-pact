FROM node:21-alpine AS node
FROM alpine:3.19

# Arguments
ARG USER_NAME="php"
ARG USER_UID="1000"
ARG GROUP_NAME="php"
ARG GROUP_GID="1000"
ARG PHP_EXTENSIONS="php83 php83-bcmath php83-ctype php83-curl php83-dom php83-fileinfo php83-fpm php83-ftp php83-gd \
    php83-iconv php83-intl php83-mbstring php83-mysqli php83-mysqlnd php83-opcache php83-openssl php83-pcntl php83-pdo \
    php83-pdo_mysql php83-pdo_sqlite php83-pecl-redis php83-pecl-xdebug php83-phar php83-posix php83-simplexml \
    php83-sockets php83-sodium php83-tokenizer php83-xml php83-xmlreader php83-xmlwriter php83-xsl php83-zip"
ENV COMPOSER_MEMORY_LIMIT="3G"

# Environment variables
ENV LANG="pt_BR.UTF-8" \
    LANGUAGE="pt_BR:br" \
    LC_ALL="pt_BR.UTF-8" \
    TZ="America/Sao_Paulo"

# Create a user and group used to launch processes
RUN addgroup -g $GROUP_GID -S $GROUP_NAME && \
    adduser -u $USER_UID -D -S -s /sbin/nologin -G $GROUP_NAME $USER_NAME

# Install softwares
RUN set -ex \
    apk update -qq && \
    apk add --no-cache tzdata ca-certificates argon2-dev mysql-client mariadb-connector-c perl libsodium-dev openssl-dev curl git patch $PHP_EXTENSIONS --upgrade grep && \
    ln -snf /usr/bin/php83 /usr/bin/php && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    rm -rf /var/lib/{apt,dpkg,cache,log}/ /tmp/* /var/tmp/*

# Forward logs and configure permissions
RUN set -ex && \
     ln -sf /dev/stderr /var/log/php83/error.log && \
     chown -R $USER_UID:$GROUP_GID /etc/php83/ && \
     chown -R $USER_UID:$GROUP_GID /var/log/php83/ && \
     mkdir -p /var/lib/php/session/ && \
     chown -R $USER_UID:$GROUP_GID /var/lib/php/session && \
     chmod 770 /var/lib/php/session/

COPY ./docker/php/conf/php.ini /etc/php83/php.ini
COPY ./docker/php/conf/php-fpm.d/www.conf /etc/php83/php-fpm.d/www.conf
COPY ./docker/php/conf/conf.d/50_xdebug.ini /etc/php83/conf.d/50_xdebug.ini

# Create app directory and set permissions
RUN set -ex && \
     mkdir /app/ && \
     chown -R 1000:1000 /app/ && \
     find /app/ -type f -exec chmod 664 {} \; && \
     find /app/ -type d -exec chmod 775 {} \;

# NODE
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && \
    ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx && \
    npm install -g npm-check-updates

# Workdir
WORKDIR /app

# User
USER $USER_NAME

EXPOSE 9000

CMD ["php-fpm83", "-F"]
