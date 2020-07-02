Symfony Security — аутентификация / авторизация
===============================================

[Статья официальной документации](https://symfony.com/doc/current/security.html)     
[Security-компонент](https://symfony.com/doc/current/components/security.html)

Security-компонент (точнее, набор компонентов) — комплексное решение для аутентификации пользователей веб-приложения. Компонент имеет несколько встроенных механизмов авторизации (HTTP-Basic, Form-login, X.509 certificate login) и позволяет легко реализовать собственную (практически любую) стратегию авторизации.

Помимо этого сам компонент предоставляет возможность базовой авторизации пользователей, основанный на ролях.

В случае с Symfony-фреймворком установка всех компонентов выполняется просто:

```shell
composer require symfony/security-bundle
```

Как уже было сказано, это **набор** компонентов:

- `symfony/security-core` — базовая функциональность;
- `symfony/security-http` — компонент для работы с Http-протоколом в контексте безопасности;
- `symfony/security-csrf` — защита от [CSRF-атак](https://en.wikipedia.org/wiki/Cross-site_request_forgery)
- `symfony/security-guard` — соединяет всё вместе, по сути, предоставляет auth-flow, путь, по которому проходит запрос к веб-приложению в контексте аутентификации.

Работа с security-компонентом внутри фреймворка
-----------------------------------------------

Начинать, естественно, рекомендуется с генерации классов. С установкой security-bundle становятся доступны команды `make:auth` (генерация Guard-аутентификатора) и `make:user` — создание класса пользователя.

```shell
> $ bin/console make:user
 
The name of the security user class (e.g. User) [User]:
> SiteUser
 Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
> yes
 Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
> email
 Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).
 Does this app need to hash/check user passwords? (yes/no) [yes]:
> yes
 created: src/Entity/SiteUser.php
 created: src/Repository/SiteUserRepository.php
 updated: src/Entity/SiteUser.php

  Success!

 Next Steps:
   - Review your new App\Entity\SiteUser class.
   - Use make:entity to add more fields to your SiteUser entity and then run make:migration.
   - Your security.yaml could not be updated automatically. You'll need to add the following config manually:

security:
    encoders:
        App\Entity\SiteUser:
            algorithm: auto

    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\SiteUser
                property: email
   - Create a way to authenticate! See https://symfony.com/doc/current/security.html
``` 

Эта команда генерирует класс (Doctrine Entity, если вы согласились с вопросом, хотите ли вы хранить пользователей в БД), имплементирующий `Symfony\Component\Security\Core\User\UserInterface`, который и будет вашим пользователем внутри приложения.

Обратите внимание: конфигурация `security.yaml` **не** меняется, вам нужно самостоятельно добавить туда строчки из примера в выводе. 

Логин через форму
-----------------

В [статье официальной документации](https://symfony.com/doc/current/security/form_login_setup.html) всё описано достаточно подробно. Начнем, как обычно, с генерации класса:

```shell
> $ bin/console make:auth

 What style of authentication do you want? [Empty authenticator]:
  [0] Empty authenticator
  [1] Login form authenticator
> 1
 The class name of the authenticator to create (e.g. AppCustomAuthenticator):
> AppFormAuthenticator
 Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
> SecurityController
 Do you want to generate a '/logout' URL? (yes/no) [yes]:
> yes

 created: src/Security/AppFormAuthenticator.php
 updated: config/packages/security.yaml
 created: src/Controller/SecurityController.php
 created: templates/security/login.html.twig

  Success!

 Next:
 - Customize your new authenticator.
 - Finish the redirect "TODO" in the App\Security\AppFormAuthenticator::onAuthenticationSuccess() method.
 - Review & adapt the login template: templates/security/login.html.twig.
```

В результате действий этой команды создаются:
- контроллер `App\Controller\SecurityController` с действием для входа и выхода;
- представление (view) `templates/security/login.html.twig` для отображения формы логина;
- главная часть — аутентификатор `App\Security\AppFormAuthenticator`

Конфигурация `security.yaml` **изменяется** — основной фаервол получает ключ `guard` и настройку, которая говорит использовать только что созданный аутентификатор.

### Аутентификатор и порядок проверки пользователя

Порядок действий аутентификации очень наглядно иллюстрирует схема:

[![Authenticator flow](https://symfony.com/doc/current/_images/security/authentication-guard-methods.svg)](https://symfony.com/doc/current/security/guard_authentication.html#the-guard-authenticator-methods)

При включенной аутентификации (установленном компоненте `security`) каждый http-запрос, попадающий под шаблон (`pattern`) фаервола или `access_control`, инициирует загрузку и выполнение аутентификатора. Порядок выполения действий регулируется самим security-компонентом:

- `supports(Request $request)` — метод, определяющий, подходит ли текущий запрос под параметры аутентификатора. Если нет, дальнейшие действия не будут выполнятся, и всё управление переходит непосредственно к security-компоненту (и если запрос попадает под действие фаервола с необходимостью авторизации, то доступ будет запрещен).     
    **Обратите внимание**, что при традиционной аутентификации при таких обстоятельствах будет предпринята попытка перенаправления на логин (см. ниже описание метода `start`), и если вы включите в параметрах `security.yaml` запрет доступа к роуту логина, ваще приложение будет бесконечно перенаправлять ваш запрос, и в итоге упадёт. Роут с отображением формы логина должен быть доступен анонмному пользователю;
- `getCredentials(Request $request)` — метод, получающий реквизиты доступа из запроса. Обычно это массив (никто, однако, не запрещает сделать собственный объект, формировать и возвращать его);
- `getUser($credentials, UserProviderInterface $userProvider)` — метод получает `$credentials` из результата предыдущего метода и класс, имплементирующий `UserProviderInterface`. Этот класс будет ровно тем, который указан в конфигурации, то есть в нашем случае — `Symfony\Bridge\Doctrine\Security\User\EntityUserProvider` (`entity` — его идентификатор).     
    **Обратите внимание** — генератор кода делает не вполне правильный метод — он работает, но не стоит загружать `EntityManagerInterface` в аутентификатор и запрашивать объект пользователя напрямую из него, это зона ответственности `UserProviderInterface`, именно его метод `loadUserByUsername` возвращает экземпляр пользователя. В примере код уже исправлен.
- `checkCredentials($credentials, UserInterface $user)` — получает массив реквизитов доступа и объект пользователя из предыдущего метода. Именно здесь проходит проверка правильности этих реквизитов — в данном случае, пароля.

Далее, в зависимости от результатов проверки будет выполнен либо `createAuthenticatedToken`, который, в свою очередь, вызовет `onAuthenticationSuccess`, либо `onAuthenticationFailure` (при отрицательной проверке). `onAuthenticationSuccess` возвращает объект `Response` или `null` — в нашем случае `RedirectResponse`, который перенаправляет пользователя на определенный роут. Если результатом будет `null`, запрос просто продолжится (актуально в случае stateless-аутентификации).

Отдельный метод `start(Request $request, AuthenticationException $authException = null)` вызывается, когда запрос подходит под правила фаервола, но не содержит нужных реквизитов, и призван показать пользователю путь аутентификации — в нашем случае, он возвращает `RedirectResponse` на страницу логина, в случае, например, авторизации по JWT — ответ со статусом 401 и описанием «не найден токен» или подобным.

### Дальнейшие действия

Сгенерируем БД-миграцию для создания таблицы пользователей:

```shell
bin/console cache:clear && bin/console make:migration
 // Clearing the cache for the dev environment with debug true
 [OK] Cache for the "dev" environment (debug=true) was successfully cleared.
  Success!
 Next: Review the new migration "migrations/Version20200702051437.php"
 Then: Run the migration with php bin/console doctrine:migrations:migrate
```

**Внимание!** Не называйте таблицу `user`, если назвали класс просто `User` — это зарезервированное слово в большинстве SQL-движков. Если ваш класс называется `User`, добавьте аннотацию класса `@ORM\Table(name="site_user")`.

После необходимых изменений, применим миграцию:

```shell
> $ bin/console doctrine:migration:migrate -n
[notice] Migrating up to DoctrineMigrations\Version20200702051437
[notice] finished in 326.8ms, used 20M memory, 1 migrations executed, 3 sql queries
```

Создадим (для простоты) фикструру, добавляющую пользователя в БД:

```shell
> $ bin/console make:fixtures AppUserFixture
 created: src/DataFixtures/AppUserFixture.php
  Success!
 Next: Open your new fixtures class and start customizing it.
 Load your fixtures by running: php bin/console doctrine:fixtures:load
```

Смотрите пример фикстуры в `App\DataFixtures\AppUserFixture`, там же обратите внимание на пример программного хэширования пароля (`UserPasswordEncoderInterface::encodePassword`).

И применим её:

```shell
> $ bin/console doctrine:fixtures:load -n
   > purging database
   > loading App\DataFixtures\AppFixtures
   > loading App\DataFixtures\AppUserFixture
```

После этого вы можете залогинится с реквизитами только что созданного пользователя.

![User is logged in](https://git.crtweb.ru/academy-of-quality/symfony-learning/-/raw/master/doc-resources/images/logged-user.png)

