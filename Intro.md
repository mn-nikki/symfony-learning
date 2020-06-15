Введение
========

Для того, чтобы изучать Symfony-фреймворк, нужно его установить и настроить. Вы можете скопировать этот репозиторий, или пойти более сложным путем и установить базу для разработки самостоятельно (предполагается, что исполняемый файл `composer` доступен для прямого запуска):

```shell
composer create-project symfony/skeleton symfony-learning
```

В проекте объявлены некоторые зависимости для разработки (если вы делаете свой проект, вам нужно их поставить):

- `friendsofphp/php-cs-fixer` и конфигурационный файл `.php_cs.dist` — приводит стиль кода к стандарту;
- `symfony/debug-pack` — предоставляет различные инструменты для отладки. В частности, функции `\dump($var)` и `dd($var)` — для вывода переменных и «вывод и отключение (dump and die)»;
- `symfony/profiler-pack` — предоставляет инструмент для профилирования запросов (время запроса, вызванные части приложения, ошибки, запросы к БД и так далее);
- `symfony/test-pack` — phpUnit и расширения для тестирования;
- `vimeo/psalm` и конфигурация `psalm.xml` — статический анализатор кода.

Среда выполнения
----------------

Для начальных этапов достаточно встроенного php-сервера. Перейдите в каталог проекта и выполните команду

```shell
php -S 127.0.0.1:8000 -t public # 127.0.0.1 is your host, 8000 — port to listening, public — the web-root directory
```

Более продвинутый вариант того же сервера — исполняемый файл разработчиков symfony. Установка (MacOS):

```shell
curl -sS https://get.symfony.com/cli/installer | bash
```

Запуск сервера (из каталога приложения):

```shell
symfony local:server:start --port=8000
```

Расширенное описание и справку смотрите на сайте производителя — https://symfony.com/doc/current/setup/symfony_server.html или в стандартном `symfony local:server:start --help`

Соглашения
----------

Когда в этом руководстве упоминается путь к файлу, по-умолчанию имеется в виду путь относительно корня проекта. Если подразумевается абсолютный путь, то запись о нём будет начинаться со слэша `/`.

Все пути — в unix-формате.

В проекте (и руководстве) используется стандартный PSR-4 composer-автолоадер. То есть все имена классов и нэймспейсы могут быть напрямую сопоставлены с путями ФС. Например, Класс `App\Kernel` относительно файловой системы располагается в файле `src/Kernel.php`.

Курс поделен на части, каждая часть — в отдельной ветке репозитория. Все вместе слито в ветку `master`. В файле [Readme](README.md) — оглавление.