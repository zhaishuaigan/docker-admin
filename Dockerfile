FROM zhaishuaigan/php:tp6

COPY ./docker/docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint
COPY ./src /app
WORKDIR /app
RUN chmod 0777 /usr/local/bin/docker-php-entrypoint
RUN cd /app && composer install --no-dev
RUN chmod 0777 /app
# ENTRYPOINT ["docker-php-entrypoint"]