FROM php:8.2-apache

# Installation de modules nécessaires
RUN docker-php-ext-install mysqli

# Copie du code source
COPY app/ /var/www/html/

# Permissions laxistes volontairement vulnérables
RUN chmod -R 777 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
