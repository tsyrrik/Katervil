# Laravel + Docker стартовый проект

Базовая заготовка приложения Laravel 11, готовая к запуску в контейнерах с PHP 8.3 (php-fpm), Nginx и PostgreSQL.

## Состав стека
- **php-fpm**: образ `php:8.3-fpm` с необходимыми расширениями (pdo_pgsql, intl, bcmath, опционально opcache).
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

`vendor` и `bootstrap/cache` живут в именованных volume (`vendor-data`, `cache-data`), поэтому при любом изменении окружения, которое удаляет volume, зависимости нужно класть заново:

```bash
docker compose down -v              # полностью пересобрать окружение
docker compose build app            # пересобираем образ
docker compose run --rm app composer install
docker compose up -d
```

Если удалить `vendor` на хосте, нужно снова выполнить `docker compose run --rm app composer install`, потому что фактические файлы находятся внутри volume.

> ⚠️ На macOS Docker Desktop автоматически использует VirtioFS для шаринга каталогов.  
> Для PHP‑проекта с большим количеством мелких файлов это может приводить к ошибкам вида  
> `Resource deadlock avoided`. В docker-compose включён режим `consistency: delegated`,  
> который смягчает проблему. При систематических ошибках переключите Docker Desktop на gRPC-FUSE  
> (Settings → Resources → File Sharing → Advanced → gRPC-FUSE) и перезапустите контейнеры.
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

## Функционал учёта
- Страница `Производство` (`/production`) — выводит таблицу изделий с возможностью сортировки по всем колонкам, фиксацию операции резки и выбор исполнителя через всплывающее окно.
- Страница `Зарплата` (`/salary`) — агрегирует выполненные операции и показывает начисления сотрудникам (кол-во × стоимость за единицу).
- Данные изделий и сотрудников инициализируются сидером (`php artisan migrate:fresh --seed`).

## Аутентификация и роли
- Используется собственная форма входа (`/login`). В сидере создаются две учетные записи:
  - Начальник смены: `boss@example.com` / `password`
  - Сотрудник: `worker@example.com` / `password`
- Просмотр страниц доступен только после авторизации (`auth` middleware).
- Фиксация операций разрешена только роли `supervisor` (middleware `role:supervisor`), поэтому кнопку «Готово» и POST-запрос видят исключительно начальники смен.
