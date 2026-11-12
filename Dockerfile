FROM php:8.2-apache

# Apache listens on $PORT on Render; default Apache config uses 80,
# so we rewrite it at container start.
RUN a2enmod rewrite

COPY . /var/www/html/

# Render injects $PORT; fall back to 80 for local docker run.
ENV PORT=80
EXPOSE 80

CMD sh -c "sed -i \"s/80/\$PORT/g\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground"
