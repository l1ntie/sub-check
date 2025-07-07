FROM php:8.2-cli

WORKDIR /app

# Установка Composer
RUN apt-get update && apt-get install -y git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копирование зависимостей и установка
COPY composer.json composer.lock ./
RUN composer install

# Копируем остальной код
COPY . .

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "index.php"]
