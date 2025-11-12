# Laravel + Docker стартовый проект

Базовая заготовка приложения Laravel 11, готовая к запуску в контейнерах с PHP 8.4 (php-fpm), Nginx и PostgreSQL.

## Состав стека
- **php-fpm**: образ `php:8.4-rc-fpm` с необходимыми расширениями (pdo_pgsql, intl, bcmath, opcache и т.д.).
- **Nginx**: проксирует HTTP-трафик и передаёт PHP-запросы в FPM.
- **PostgreSQL 16**: отдельный контейнер с сохранением данных в volume `pgdata`.

## Предварительные требования
1. Docker Desktop или Docker Engine 24+ с плагином Compose v2.
2. Скопируйте файл окружения и задайте свои значения при необходимости:
   ```bash
   cp .env.example .env
   ```

## Первый запуск
```bash
docker compose build                # собираем образ php-fpm
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d                # стартуем все сервисы
```
Приложение будет доступно по адресу http://localhost:8080.

## Полезные команды
- `docker compose exec app php artisan migrate` – выполнить миграции.
- `docker compose exec app php artisan test` – прогнать тесты.
- `docker compose exec app npm install` + `npm run dev` – сборка фронтенда внутри контейнера.
- `docker compose logs -f app` – посмотреть логи выбранного сервиса.
- `docker compose down -v` – остановить и удалить контейнеры вместе с данными PostgreSQL.

## Настройка
- Переменные окружения для базы уже прописаны в `.env` (`DB_HOST=postgres`, `DB_DATABASE=laravel`, `DB_USERNAME=laravel`, `DB_PASSWORD=secret`). Их же использует контейнер PostgreSQL.
- Порт приложения на хосте задаётся в `docker-compose.yml` (по умолчанию `8080`).
- Пользователь `www-data` внутри контейнера получает UID/GID из аргументов сборки `USER_ID`/`GROUP_ID`, что позволяет избежать конфликтов прав при необходимости.

## Структура Docker
```
docker/
├── nginx/
│   └── default.conf        # конфигурация веб-сервера
└── php/
    ├── Dockerfile          # образ php-fpm + composer + расширения
    └── conf.d/
        └── laravel.ini     # базовые PHP-настройки (memory_limit, opcache)
```
