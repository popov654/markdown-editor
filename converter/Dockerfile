FROM php:8.2-apache
RUN apt-get update && apt-get install -y imagemagick && apt-get install -y webp && apt-get clean
COPY ./articles /var/www/html/articles
COPY ./uploads /var/www/html/uploads
COPY converter/convert.sh /var/www/html/scripts/convert.sh
