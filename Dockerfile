FROM php:7.3-apache

COPY wwwroot/ /var/www/html

EXPOSE 80

CMD apachectl -DFOREGROUND