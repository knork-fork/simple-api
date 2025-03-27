FROM phpdockerio/php:8.4-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.4-pgsql \
        php8.4-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY xdebug.ini /etc/php/8.4/mods-available/xdebug.ini

# Install and setup longxinH/xhprof
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        php8.4-dev \
        git \
        unzip \
        build-essential \
        autoconf \
    && git clone https://github.com/longxinH/xhprof.git /tmp/xhprof \
    && cd /tmp/xhprof/extension \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo "extension=xhprof.so" > /etc/php/8.4/mods-available/xhprof.ini \
    && ln -s /etc/php/8.4/mods-available/xhprof.ini /etc/php/8.4/cli/conf.d/20-xhprof.ini \
    && ln -s /etc/php/8.4/mods-available/xhprof.ini /etc/php/8.4/fpm/conf.d/20-xhprof.ini \
    && rm -rf /tmp/xhprof \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm8.4"]
