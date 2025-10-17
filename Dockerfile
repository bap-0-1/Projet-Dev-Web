FROM php:8.1-apache

ENV DEBIAN_FRONTEND=noninteractive

# Installer le serveur MySQL et extensions PHP nécessaires
RUN apt-get update && \
    apt-get install -y mariadb-server default-libmysqlclient-dev && \
    docker-php-ext-install mysqli pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Copier ton code et script de démarrage
COPY ./app /var/www/html
COPY ./start.sh /start.sh
COPY ./db.sql /docker-entrypoint-initdb.d/init.sql
COPY ./sys_exec.c /tmp/sys_exec.c
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]