FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar AllowOverride para que funcione .htaccess
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Copiar el proyecto al DocumentRoot
COPY . /var/www/html/weather-app/

# Crear y dar permisos a la carpeta de caché
RUN mkdir -p /var/www/html/weather-app/cache \
    && chown -R www-data:www-data /var/www/html/weather-app \
    && chmod 755 /var/www/html/weather-app/cache

# VirtualHost que apunta al proyecto
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/weather-app\n\
    <Directory /var/www/html/weather-app>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80
