FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones de PHP (especialmente pdo_pgsql para Supabase)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar el código de la aplicación (el contexto será la carpeta backend)
COPY . .

# Instalar las dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto que Render asigna
EXPOSE 10000

# Comando para iniciar el servidor de Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}