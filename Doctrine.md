Doctrine, ORM-паттерн, отличия и использование Data Mapper и Active Record
==========================================================================

[Статья официальной документации](https://symfony.com/doc/current/doctrine.html)

Doctrine — набор компонентов (библиотек), который позволяет использовать базы данных в symfony-приложении.

Установка и настройка
---------------------

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

Создание сущностей (моделей), миграции
--------------------------------------

Чтобы продемонстрировать работу сущностей БД и связей, создадим (и в дальнейшем будем развивать) что-то похожее на реальную структуру. Например, меню пиццерии. Схема будет примерно такая:

- **Пицца** с характеристиками:
    - название
    - описание
    - диаметр
    - составные части
- **Часть рецепта** — только вес и ингредиент
- **Ингредиент** — отдельный продукт, из комплекта этих продуктов в итоге состоит пицца

Обратите внимание, что мы не учитываем (пока) в приготовлении пиццы тесто, работу, электроэнергию и прочие подобные вещи — наша задача сейчас посмотреть, как работают связи БД, базнес-логику с ценнобразованием будем реализовывать потом.

Официальный мануал предлагает создавать сущности с помощью генератора кода (и это хороший способ для простой структуры). В этом нам поможет команда `bin/console make:entity`

```shell
bin/console make:entity Ingredient

 created: src/Entity/Ingredient.php
 created: src/Repository/IngredientRepository.php

 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > title

 Field type (enter ? to see all types) [string]:
 > string

 Field length [255]:
 > 255

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Ingredient.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > description

 Field type (enter ? to see all types) [string]:
 > text

 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Ingredient.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > price

 Field type (enter ? to see all types) [string]:
 > integer

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Ingredient.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >

  Success!

 Next: When you're ready, create a migration with php bin/console make:migration
```

Всё довольно очевидно — надо отвечать на вопросы и код генерируется сам. Стоит обратить внимание на создание сущностей со связями:

```shell
bin/console make:entity ReceiptPart

 created: src/Entity/ReceiptPart.php
 created: src/Repository/ReceiptPartRepository.php

 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > weight

 Field type (enter ? to see all types) [string]:
 > integer

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/ReceiptPart.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > ingredient

 Field type (enter ? to see all types) [string]:
 > ?

Main types
  * string
  * text
  * boolean
  * integer (or smallint, bigint)
  * float

Relationships / Associations
  * relation (a wizard 🧙 will help you build the relation)
  * ManyToOne
  * OneToMany
  * ManyToMany
  * OneToOne

Array/Object Types
  * array (or simple_array)
  * json
  * object
  * binary
  * blob

Date/Time Types
  * datetime (or datetime_immutable)
  * datetimetz (or datetimetz_immutable)
  * date (or date_immutable)
  * time (or time_immutable)
  * dateinterval

Other Types
  * decimal
  * guid
  * json_array


 Field type (enter ? to see all types) [string]:
 > ManyToOne

 What class should this entity be related to?:
 > Ingredient

 Is the ReceiptPart.ingredient property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to add a new property to Ingredient so that you can access/update ReceiptPart objects from it - e.g. $ingredient->getReceiptParts()? (yes/no) [yes]:
 > yes

 A new property will also be added to the Ingredient class so that you can access the related ReceiptPart objects from it.

 New field name inside Ingredient [receiptParts]:
 > receiptParts

 Do you want to activate orphanRemoval on your relationship?
 A ReceiptPart is "orphaned" when it is removed from its related Ingredient.
 e.g. $ingredient->removeReceiptPart($receiptPart)

 NOTE: If a ReceiptPart may *change* from one Ingredient to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\ReceiptPart objects (orphanRemoval)? (yes/no) [no]:
 > no

 updated: src/Entity/ReceiptPart.php
 updated: src/Entity/Ingredient.php
```

В момент добавления свойства `ingredient` мы задали тип связи — `ManyToOne`, то есть множество **частей** могут указывать на один и тот же **ингредиент**, что логично — части рецепта «оливки, 150гр.» и «оливки, 50гр.» будут связаны с одними и теми же оливками.

Таким же образом делаем и третью сущность — собственно, пиццу, которая будет иметь свойство `parts`, связанное как `OneToMany` (один ко многим) с частями рецепта.

### Посмотрим, что получилось. 

Как вы можете видеть, у нас были созданы три класса в нэймспейсе `App\Entity`. Каждый из этих классов является сущностью БД, то есть его поля (свойства) — это поля базы данных. Обратите внимание на аннотации этих полей, особенно на поля со связями, посмотрите, как они устроены.

Обратите внимание на конфигурацию приложения: в конфигурационном файле `config/packages/doctrine.yaml` указан параметр `orm.mappings`, и в нем перечислены все маппинги классов в БД (в нашем случае только один, но понятно, что их может быть несколько), а в `config/services.yaml` директория `src/Entity` исключена из тех ресурсов, которые автоматически объявляются как сервисы.

Это значит, что мы **не можем** загрузить какой-то сервис в класс сущности, но по идее нам это и **не нужно** — классы сущностей имеют единственную ответственность, и эта ответственность — представление данных БД в виде экземпляра класса. С другой стороны все репозитории — это полноправные сервисы, поэтому мы можем в любых местах нашего приложения получать их в конструкторы классов без дополнительной конфигурации.

В результате операций, что мы проделали, были созданы и три класса в нэймспейсе `App\Repository` — это, как несложно догадаться, репозитории (источники данных) для наших сущностей.

Здесь мы вживую видим отличие паттерна ActiveRecord и DataMapper. В случае ActiveRecord наши сущности (модели) обязательно наследовались бы от какого-то глобального класса и сами несли бы в себе возможность запрашивать и сохранять данные, в нашем же случае мы имеем чистые классы (только с геттерами и сеттерами) и отдельный механизм получения данных из БД и записи их.

Сгенерированные репозитории практически пусты, более того — необязательны. Если получение вашей сущности не предполагает каких-то сложных (и используемых в разных местах) запросов, вы можете в её аннотации писать просто `@ORM\Entity()`, без указания `repositoryClass`.

В заключение о создании сущностей можно сказать, что не стоит увлекаться автоматической генерацией кода: к примеру, получение ID может быть вынесено в трайт, может быть ситуация, когда ваши сущности имплементируют некий интерфейс и так далее. Более того, вы можете воспользоваться только частью генератора — сделайте класс, опишите только его свойства (без геттеров и сеттеров), и дайте команду `make:entity App\\Entity\\YourEntity --regenerate`. При таких обстоятельствах генератор сделает только методы для получения и установки свойств. 

Этой же командой можно воспользоваться, если вы добавляете что-то к уже существующим моделям.

### Миграции

Для миграций предусмотрен собственный генератор, и даже в двух видах: `make:migration` и `doctrine:migrations:diff`. Делают они одно и то же, первый (`make:migration`) по сути дела вызывает второй.

У нас три сущности, а значит, будет три таблицы БД. Перед запуском генератора миграций всегда рекомендуется очищать кэш приложения. Запустим команды:

```shell
bin/console cache:clear
[OK] Cache for the "dev" environment (debug=true) was successfully cleared.

bin/console make:migration

  Success!

 Next: Review the new migration "migrations/Version20200617041311.php"
 Then: Run the migration with php bin/console doctrine:migrations:migrate
 See https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html
```

В результате этой команды в каталоге `migrations` был создан класс `Version<date>`, который, собственно, и содержит изменения, которые получит база данных. Обратите внимание:

- метод `getDescription` возвращает пустую строку. Его следует изменять так, чтобы при выводе была понятна суть миграции. 
- в случае с БД PostgreSQL в методе `down` образуется лишнее действие (это баг) — `$this->addSql('CREATE SCHEMA public');`, то есть генератор неверно воспринимает структуру БД PostgreSQL и считает, что при откате миграции нужно создавать public-схему. Это не так, и эту строку следует удалить.

Также мы можем видеть отличие движков MySQL и PostgreSQL — в PostgreSQL автоинкремент реализован специальной сущностью `SEQUENCE`.

Посмотрим на результат. Сначала проверим, как там наша БД:

```shell
bin/console doctrine:schema:validate
Mapping
-------
 [OK] The mapping files are correct.
Database
--------
 [ERROR] The database schema is not in sync with the current mapping file.
```

Всё, как и ожидалось — у нас есть сущности, которые маппят БД, но нет самих таблиц БД. Посмотрим на статус миграций:

```shell
bin/console doctrine:migration:status
+----------------------+----------------------+------------------------------------------------------------------------+
| Configuration                                                                                                        |
+----------------------+----------------------+------------------------------------------------------------------------+
| Storage              | Type                 | Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration |
|                      | Table Name           | doctrine_migration_versions                                            |
|                      | Column Name          | version                                                                |
|----------------------------------------------------------------------------------------------------------------------|
| Database             | Driver               | Doctrine\DBAL\Driver\PDOPgSql\Driver                                   |
|                      | Name                 | symfony_learn                                                          |
|----------------------------------------------------------------------------------------------------------------------|
| Versions             | Previous             | 0                                                                      |
|                      | Current              | 0                                                                      |
|                      | Next                 | DoctrineMigrations\Version20200617041311                               |
|                      | Latest               | DoctrineMigrations\Version20200617041311                               |
|----------------------------------------------------------------------------------------------------------------------|
| Migrations           | Executed             | 0                                                                      |
|                      | Executed Unavailable | 0                                                                      |
|                      | Available            | 1                                                                      |
|                      | New                  | 1                                                                      |
|----------------------------------------------------------------------------------------------------------------------|
| Migration Namespaces | DoctrineMigrations   | /Users/andrew/Sites/creative/Courses/symfony-learning/migrations       |
+----------------------+----------------------+------------------------------------------------------------------------+
```

Как мы можем видеть, у нас есть одна непримененная миграция. Применим её:

```shell
bin/console doctrine:migration:migrate -n
[notice] Migrating up to DoctrineMigrations\Version20200617041311
[notice] finished in 273.4ms, used 18M memory, 1 migrations executed, 10 sql queries
```

И посмотрим на результат:

```shell
bin/console doctrine:migration:status
+----------------------+----------------------+------------------------------------------------------------------------+
| Configuration                                                                                                        |
+----------------------+----------------------+------------------------------------------------------------------------+
| Storage              | Type                 | Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration |
|                      | Table Name           | doctrine_migration_versions                                            |
|                      | Column Name          | version                                                                |
|----------------------------------------------------------------------------------------------------------------------|
| Database             | Driver               | Doctrine\DBAL\Driver\PDOPgSql\Driver                                   |
|                      | Name                 | symfony_learn                                                          |
|----------------------------------------------------------------------------------------------------------------------|
| Versions             | Previous             | 0                                                                      |
|                      | Current              | DoctrineMigrations\Version20200617041311                               |
|                      | Next                 | Already at latest version                                              |
|                      | Latest               | DoctrineMigrations\Version20200617041311                               |
|----------------------------------------------------------------------------------------------------------------------|
| Migrations           | Executed             | 1                                                                      |
|                      | Executed Unavailable | 0                                                                      |
|                      | Available            | 1                                                                      |
|                      | New                  | 0                                                                      |
|----------------------------------------------------------------------------------------------------------------------|
| Migration Namespaces | DoctrineMigrations   | /Users/andrew/Sites/creative/Courses/symfony-learning/migrations       |
+----------------------+----------------------+------------------------------------------------------------------------+

bin/console doctrine:schema:validate
Mapping
-------
 [OK] The mapping files are correct.
Database
--------
 [OK] The database schema is in sync with the mapping files.
```

Фикстуры (демо-данные)
----------------------

Чтобы проверить работу наших классов сущностей, нам понадобятся данные. В тестовых целях и целях разработки (но не на production-площадках!) мы можем использовать специальные пакеты для наполнения БД:

- `doctrine/doctrine-fixtures-bundle` (алиас `orm-fixtures`) — пакет для записи в БД сгенерированных данных;
- `fzaninotto/faker` — пакет для генерации даннных

Установим пакеты:

```shell
composer require --dev orm-fixtures fzaninotto/faker
```

В результате установки `orm-fixtures` будет создан класс `App\DataFixtures\AppFixtures`, и обратите внимание — он создаётся несколько неверно. В блоке подключения классов используется `Doctrine\Common\Persistence\ObjectManager`, но нужен `use Doctrine\Persistence\ObjectManager` (без Common), это нужно исправить.

Этим классом можно воспользоваться для записи данных в БД.

Некоторые тонкости
------------------

Сущности БД могут быть связаны с разной степенью необходимости получения связей. То есть мы, к примеру, можем иметь пользователя и его профиль, которые всегда (за очень редким исключением) должны быть вместе. С другой стороны, у пользователя, к примеру, есть список друзей, и не всегда его друзья будут отображаться вместе с пользователем.

В нашем случае ингредиенты пиццы практически всегда будут вместе с ней, и на этот случай есть аннотация `fetch="EAGER"`, которая говорит ORM, что нужно запрашивать эту связь через JOIN, а не отдельно.

Также стоит обратить внимание на каскадное сохранение связи: предположим, мы создали `ReceiptPart` (не записали его), присоединили этот `ReceiptPart` к только что созданной `Pizza` и пытаемся её сохранить.

Получим ошибку, потому что ORM не понимает, как именно сохранять сущности — на уровне БД они связаны по ID, и ни у одной, ни у другой этого ID нет.

На этот случай у связи `OneToMany` существует аннотация `cascade`, в нашем случае `cascade={"persist"}`, которая говорит, что нужно сохранять все связанные сущности каскадно.
