FROM alpine:3.18

# Arguments
ARG USER_NAME="php"
ARG USER_UID="1000"
ARG GROUP_NAME="php"
ARG GROUP_GID="1000"
ARG PHP_EXTENSIONS="php82 php82-bcmath php82-ctype php82-curl php82-dom php82-fileinfo php82-fpm php82-ftp php82-gd \
    php82-iconv php82-intl php82-mbstring php82-mysqli php82-mysqlnd php82-opcache php82-openssl php82-pcntl php82-pdo \
    php82-pdo_mysql php82-pecl-redis php82-pecl-xdebug php82-phar php82-posix php82-simplexml php82-sockets \
    php82-sodium php82-tokenizer php82-xml php82-xmlreader php82-xmlwriter php82-xsl php82-zip"
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
    apk add tzdata ca-certificates argon2-dev libsodium-dev openssl-dev curl git patch $PHP_EXTENSIONS && \
    ln -snf /usr/bin/php82 /usr/bin/php && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    rm -rf /var/lib/{apt,dpkg,cache,log}/ /tmp/* /var/tmp/*

# Forward logs and configure permissions
RUN set -ex && \
     ln -sf /dev/stderr /var/log/php82/error.log && \
     chown -R $USER_UID:$GROUP_GID /etc/php82/ && \
     chown -R $USER_UID:$GROUP_GID /var/log/php82/ && \
     mkdir -p /var/lib/php/session/ && \
     chown -R $USER_UID:$GROUP_GID /var/lib/php/session && \
     chmod 770 /var/lib/php/session/

 # Create app directory and set permissions
RUN set -ex && \
     mkdir /app/ && \
     chown -R 1000:1000 /app/ && \
     find /app/ -type f -exec chmod 664 {} \; && \
     find /app/ -type d -exec chmod 775 {} \;

#RUN sed -i '/^default = default_sect/a legacy = legacy_sect' /etc/ssl/openssl.cnf
#RUN sed -i '/^\[default_sect\]/a activate = 1' /etc/ssl/openssl.cnf
#RUN printf "[legacy_sect]\nactivate = 1" >> /etc/ssl/openssl.cnf

# Workdir
WORKDIR /app

# User
USER $USER_NAME

EXPOSE 9000
CMD ["php-fpm82", "-F"]
