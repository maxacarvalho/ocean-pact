FROM alpine:3.17

# Arguments
ARG USER_NAME="php"
ARG USER_UID="1000"
ARG GROUP_NAME="php"
ARG GROUP_GID="1000"
ARG PHP_EXTENSIONS="php81 php81-fpm php81-mysqlnd php81-mysqli php81-xml php81-opcache php81-pdo php81-pdo_mysql \
    php81-pdo php81-redis php81-bcmath php81-ctype php81-intl php81-xsl php81-sockets php81-json php81-openssl php81-phar \
    php81-zlib php81-mbstring php81-zip php81-gd php81-tokenizer php81-xmlwriter php81-dom php81-curl php81-fileinfo \
    php81-simplexml php81-iconv php81-pcntl php81-xmlreader php81-posix php81-sodium php81-ftp php81-pecl-xdebug"
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
    apk add tzdata ca-certificates openssl curl git patch $PHP_EXTENSIONS && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    rm -rf /var/lib/{apt,dpkg,cache,log}/ /tmp/* /var/tmp/*

 # Forward logs and configure permissions
 RUN set -ex && \
     ln -sf /dev/stderr /var/log/php81/error.log && \
     chown -R $USER_UID:$GROUP_GID /etc/php81/ && \
     chown -R $USER_UID:$GROUP_GID /var/log/php81/ && \
     mkdir -p /var/lib/php/session/ && \
     chown -R $USER_UID:$GROUP_GID /var/lib/php/session && \
     chmod 770 /var/lib/php/session/

 # Create app directory and set permissions
 RUN set -ex && \
     mkdir /app/ && \
     chown -R 1000:1000 /app/ && \
     find /app/ -type f -exec chmod 664 {} \; && \
     find /app/ -type d -exec chmod 775 {} \;

# Workdir
WORKDIR /app

# User
USER $USER_NAME

# Override stop signal to stop process gracefully
STOPSIGNAL SIGQUIT

EXPOSE 9000
CMD ["php-fpm81", "-F"]
