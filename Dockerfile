FROM ubuntu:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && apt-get install mariadb-server apache2-bin libapache2-mod-php7.4 php7.4-gd php7.4-mysql php7.4-mbstring php7.4-bcmath php7.4-json php7.4-snmp -y

# RUN mkdir /var/www/racktables

# RUN chown -R www-data:www-data /var/www/racktables

COPY racktables.conf /etc/apache2/sites-available/racktables.conf

# COPY dir.conf /etc/apache2/mods-enabled/dir.conf

RUN a2enmod dir

COPY wwwroot/ /usr/local/racktables

RUN ln -s /usr/local/racktables /var/www

RUN touch /usr/local/racktables/inc/secret.php && chown www-data:nogroup -R /var/www/racktables && chmod -R 0400 /usr/local/racktables

RUN service apache2 start && a2ensite racktables && a2dissite 000-default && service apache2 reload 

RUN service mysql start

RUN printf "[mysqld]\ncharacter-set-server=utf8\n" > /etc/mysql/conf.d/charset.cnf

COPY tests/ci_setup_mysql.sh /tmp

RUN chmod +x /tmp/ci_setup_mysql.sh && /tmp/ci_setup_mysql.sh racktables_db racktables $(dd if=/dev/random bs=16 count=1 status=none | od -A n -t x | tr -d ' ')

EXPOSE 80

CMD apachectl -DFOREGROUND