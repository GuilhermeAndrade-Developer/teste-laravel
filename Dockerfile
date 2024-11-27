FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta
EXPOSE 8000

# Comando para iniciar o servidor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
