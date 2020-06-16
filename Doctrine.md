Doctrine, ORM-паттерн, отличия и использование Data Mapper и Active Record
--------------------------------------------------------------------------

[Статья официальной документации](https://symfony.com/doc/current/doctrine.html)

Doctrine — набор компонентов (библиотек), который позволяет использовать базы данных в symfony-приложении.

Установка и настройка
=====================

Нам понадобятся два пакета:
- `symfony/orm-pack` — включает в себя непосредственно `doctrine/orm`, `doctrine/doctrine-bundle` (бандл для symfony, объявляет сервисы и настройки конфигурации) и `doctrine/doctrine-migrations-bundle` (бандл для миграций);
- `symfony/maker-bundle` (для dev-окружения) — генератор кода для автоматического создания разных классов в соответствии с принятыми соглашениями.

Установка:

```shell
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
```

Мы будем использовать БД [PostgreSQL](https://www.postgresql.org), поэтому нам понадобится эта СУБД. Разместим её в контейнере, который сконфигурируем с помощью docker-compose (полную конфигурацию смотрите в `docker-compose.yml`):

```yaml
database:
  image: postgres:11-alpine
  ports:
    - 5432:5432
  volumes:
    - database:/var/lib/postgresql/data
  environment:
    POSTGRES_PASSWORD: webmaster
    POSTGRES_USER: webmaster
    POSTGRES_DB: symfony_learn
    PGDATA: /var/lib/postgresql/data/pgdata
```

Проверьте (`docker-compose config`) и запустите (`docker-compose up -d`) контейнер с базой данных.

Установка пакетов (с symfony flex) добавила так же конфигурацию БД и миграций, ссылки на бандлы в `config/bundles.php` и строку коннекта (dsn) в `.env`-файл.

**Удалите** значение `DATABASE_URL` из `.env`, и поместите в свой локальный файл `.env.local` строку коннекта с соответствующими значениями имени БД, пользователя и пароля. Примерно так:

```shell
# <driver>://<username>:<password>@<database host>/<database name>?<options>
DATABASE_URL=postgresql://webmaster:webmaster@127.0.0.1/symfony_learn?serverVersion=11&charset=utf8
```

Обратите внимание, что здесь подразумевается, что мы запускаем сам интерпретатор php **локально**, и для доступа к веб-окружению используется [локальный symfony-сервер](https://symfony.com/doc/current/setup/symfony_server.html). Краткое описание соглашений и установки [здесь](Intro.md).

Проверить соединение с БД можно из консоли командой 

```shell
bin/console doctrine:schema:validate
```

Естественно, у нас нет пока никакой схемы и никаких таблиц в БД (мы их не создали), но эта команда поможет нам понять, что наши настройки верны и соединение с БД может быть установлено.
