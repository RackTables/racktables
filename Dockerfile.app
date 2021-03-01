FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && apt-get install apache2-bin libapache2-mod-php7.4 php7.4-gd php7.4-mysql php7.4-mbstring php7.4-bcmath php7.4-json php7.4-snmp -y

COPY . /app

RUN cp /app/racktables.conf /etc/apache2/sites-available/racktables.conf

RUN a2enmod dir

RUN ln -s /app/wwwroot /var/www/racktables

RUN a2ensite racktables && a2dissite 000-default

RUN touch /app/wwwroot/inc/secret.php && chown www-data:nogroup -R /app/wwwroot && chmod -R 0700 /app/wwwroot && chmod a=rw /app/wwwroot/inc/secret.php

EXPOSE 80

CMD apachectl -DFOREGROUND