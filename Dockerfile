FROM ubuntu:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && apt-get install apache2-bin libapache2-mod-php7.4 php7.4-gd php7.4-mysql php7.4-mbstring php7.4-bcmath php7.4-json php7.4-snmp -y

RUN mkdir /var/www/racktables

RUN chown -R www-data:www-data /var/www/racktables

COPY racktables.conf /etc/apache2/sites-available/racktables.conf

COPY dir.conf /etc/apache2/mods-enabled/dir.conf

COPY wwwroot/ /var/www/racktables

RUN touch /var/www/racktables/inc/secret.php && chown www-data:nogroup /var/www/racktables/inc/secret.php && chmod a=rw /var/www/racktables/inc/secret.php

RUN service apache2 start && a2ensite racktables && a2dissite 000-default && service apache2 reload 

# RUN mysql -u root -e "CREATE DATABASE racktables_db CHARACTER SET utf8 COLLATE utf8_general_ci;" && mysql -u root -e "CREATE USER racktables_user@localhost IDENTIFIED BY 'MY_SECRET_PASSWORD';" && mysql -u root -e "GRANT ALL PRIVILEGES ON racktables_db.* TO racktables_user@localhost;" && mysql -u root -e "flush PRIVILEGES;"

EXPOSE 80

CMD apachectl -DFOREGROUND