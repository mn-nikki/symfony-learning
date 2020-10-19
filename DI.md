Контейнер зависимостей как имплементация DI / IoC паттернов
===========================================================

Статьи официальной документации: [Dependency injection](https://symfony.com/doc/current/components/dependency_injection.html) и [Service Container](https://symfony.com/doc/current/service_container.html)

«Dependency inversion principle» в SOLD гласит, что класс должен зависеть не от конкретных имплементаций сервисов, а от абстракций, то есть интерфейсов. Такой принцип позволяет легко подменять одну конкретную реализацию другой и не зависеть от конкретных версий библиотек или сервисов — главное, чтобы новая реализация выражала тот же интерфейс, что и предыдущая.

Обратите внимание, что принцип SOLID назвается Dependency **inversion** (инверсия), а контейнер — Dependency **Injection** (внедрение), что в принципе позволяет использовать контейнер не вполне правильно, то есть как просто сборник ссылок на конкретные классы, нарушая тот самый принцип. Такое «нарушение» часто не является именно нарушенем: если вы делаете конкретный сервис для конкретного действия — делайте, но следует быть уверенным в том, что этот сервис никогда, ни при каких обстоятельствах не будет нуждаться в замене.

Как это работает
----------------

На самом базовом уровне контейнер зависимостей является именно хранилищем указателей на классы или даже методы. Достаточно реализовать простой интерфейс

```php
<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Psr\Container;

/**
 * Describes the interface of a container that exposes methods to read its entries.
 */
interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id);

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id);
}
```

и у вас уже есть контейнер зависимостей. 

Обратите внимание, что в этом интерфейсе нет методов `set…`. Их нет именно потому, что загрузка сервисов в этот контейнер предполагается именно по принципу инверсии: не контейнер получает сервис, а некая фабрика / билдер получает контейнер, и в этот контейнер уже прописывается. Естественно, в конкретной реализации не обойтись без `set…`-методов, но про них знает только билдер, а не приложение, а в приложении везде фигурирует только `ContainerInterface`.

```php
use UltraLite\Container\Container;
use App\Provider\AppProvider;
use App\Provider\DoctrineOrmProvider;

$container = new Container();
$providers = [
    AppProvider::class,
    DoctrineOrmProvider::class,
];

foreach ($providers as $providerClassName) {
    $provider = new $providerClassName;
    if (!($provider instanceof ServiceProviderInterface)) {
            throw new RuntimeException(sprintf('%s class is not a Service Provider', $providerClassName));
        }
    $provider->register($container);
}
```

```php
class AppProvider implements ServiceProviderInterface
{
    public function register(Container $container) {
        $container->set(GuzzleAdapter::class, static function (ContainerInterface $container) {
            $config = $container->get(Config::class)->get('httpClient');
            $guzzle = new GuzzleClient($config ?? []);

            return new GuzzleAdapter($guzzle);
        });
    
        $container->set(ClientInterface::class, static function (ContainerInterface $container) {
            return $container->get(GuzzleAdapter::class);
        });
    }
}
```

В приведенном примере именно с помощью созданных вручную имплементаций `ServiceProviderInterface` конфигурируется приложение и все сервисы внутри него. Современные фреймворки идут дальше и предлагают автоконфигурацию контейнера и сервисов.

Создание и использование сервисов внутри Symfony-framework
----------------------------------------------------------

Рассмотрим создание сервиса для получения, изменения и сохранения сущностей БД.

Общепринятой (и правильной) практикой является следующая схема:

- сначала создаётся [интерфейс](src/Service/PizzaManagerInterface.php) для сервиса, в котором описаны CRUD-методы;
- после этого — имплементация этого интерфейса, с конкретными выражениями этих методов;
- затем конфигурация сервиса (при необходимости);
- и после этого сервис готов к использованию.

В случае приведенного примера мы не нуждаемся в отдельной конфигурации сервиса благодаря опции `autowire: true` в настройках сервисов. Это означает, что с помощью указания (type-hint) интерфейса, который имплементируется сервисом, мы получаем тот самый класс.

Это выглядит как магия, но магией не является — интерфейс (или непосредственно имя класса) является идентификатором сервиса, и `ContainerBuilder` при компиляции контейнера ищет сервис (класс), соответствующий этому идентификатору, создаёт его экземпляр и затем передаёт его в конструктор нашего класса.

На практике это выглядит так:

```php
<?php

namespace ContainerVDZEvtT;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getPizzaControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\PizzaController' shared autowired service.
     *
     * @return \App\Controller\PizzaController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/PizzaController.php';
        include_once \dirname(__DIR__, 4).'/src/Service/PizzaManagerInterface.php';
        include_once \dirname(__DIR__, 4).'/src/Service/PizzaManager.php';

        $container->services['App\\Controller\\PizzaController'] = $instance = new \App\Controller\PizzaController(
            new \App\Service\PizzaManager(
                ($container->services['doctrine.orm.default_entity_manager'] ?? $container->load('getDoctrine_Orm_DefaultEntityManagerService')), 
                ($container->privates['App\\Repository\\PizzaRepository'] ?? $container->load('getPizzaRepositoryService')), 
                ($container->privates['monolog.logger'] ?? $container->load('getMonolog_LoggerService'))
            ), 
            ($container->services['translator'] ?? $container->getTranslatorService())
        );

        $instance->setContainer((new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'doctrine' => ['services', 'doctrine', 'getDoctrineService', false],
            'http_kernel' => ['services', 'http_kernel', 'getHttpKernelService', false],
            'parameter_bag' => ['privates', 'parameter_bag', 'getParameterBagService', false],
            'request_stack' => ['services', 'request_stack', 'getRequestStackService', false],
            'router' => ['services', 'router', 'getRouterService', false],
            'session' => ['services', 'session', 'getSessionService', true],
            'twig' => ['services', 'twig', 'getTwigService', false],
        ], [
            'doctrine' => '?',
            'http_kernel' => '?',
            'parameter_bag' => '?',
            'request_stack' => '?',
            'router' => '?',
            'session' => '?',
            'twig' => '?',
        ]))->withContext('App\\Controller\\PizzaController', $container));

        return $instance;
    }
}
```

Подробнее об этом почитать можно [здесь](https://symfony.com/doc/current/service_container/autowiring.html)

В целом можно сказать, что при нормальной настройке контейнера зависимостей (`services.yaml`) вам не придется беспокоиться о том, как конфигурировать и подгружать ваши сервисы — просто используйте указание интерфейса сервиса в конструкторе.

Параметры настройки сервисов
----------------------------

### Параметры

Отдельная секция в `services.yaml` — `parameters`. Здесь хранятся различные строковые параметры, обычно загружаемые из переменных среды:

```yaml
parameters:
    admin_email: '%env(ADMIN_EMAIL)%'
```

-----------------------------

Есть несколько ключей конфигурации для сервисов в `config/services.yaml`:

### `arguments` 

Передаёт аргументы в конструктор сервиса. Обратите внимание — если сервис получает другой сервис как аргумент, то в конфигурации идентификатор этого сервиса должен начинаться с `@`. 
- `'doctrine.orm.entity_manager'` — строка
- `'@doctrine.orm.entity_manager'` — сервис

Можно также передавать аргументы по имени переменной. К примеру, нам нужно передать в сервис некое значение, которое есть в параметрах контейнера, но оставить остальное на Autowiring:

```php
use Doctrine\ORM\EntityManagerInterface;

class SomeService {
    public function __construct(EntityManagerInterface $em, string $adminEmail) {
    
    }
}
```

Если мы попытаемся запустить приложение с таким сервисом, мы получим ошибку 

> Cannot autowire service "SomeService": argument "$adminEmail" of method "__construct()" must have a type-hint or be given a value explicitly.

В этом случае мы должны сделать примерно такой конфиг

```yaml
SomeService:
  arguments:
    $adminEmail: '%admin_email%'
```

### `calls`

Вызовы методов сервиса, обычно `set`-методов. Используется для инъекции других сервисов

```php
class SomeService
{
    private ?MailerInterface $mailer = null;

    public function setMailer(MailerInterface $mailer): self 
    {
        $this->mailer = $mailer;

        return $this;
    }

    private function getMailer(): ?MailerInterface
    {
        return $this->mailer;
    }
}
```

Конфигурация:

```yaml
SomeService:
  calls:
    - [setMailer, ['@mailer']]
```

### `tags`

Тэги используются Symfony-приложением для регистрации сервиса с некими специальными параметрами. К примеру, такая конфигурация

```yaml
services:
  App\Twig\AppExtension:
    tags: ['twig.extension']
```

говорит приложению, что при инициализации `TwigBundle` нужно собрать все сервисы с такими тэгами и зарегистрировать их как расширения twig. 

Это происходит благодаря тому, что в `TwigBundle` этот тэг зарегистрирован, и после того, как контейнер собирает все сервисы с этим тэгом, ядро Twig может их использовать. 

Тэги нужны именно для конфигурации ваших сервисов как частей чего-то большего, или же при создании сложных бандлов со многими включенными сервисами, для получения всех этих сервисов.

Импорт конфигураций
-------------------

Иногда (особенно в приложениях со сложной логикой) файл определений сервисов вырастает до значительных размеров, и хочется логически разделить конфигурацию сервисов для разных частей приложения. Для этого существует специальный синтаксис `imports`, который позволяет загружать отдельные конфигурационные файлы в основной.

```yaml
imports:
  - { resource: '%kernel.project_dir%/config/easy-admin/*/*.yaml' }
```

Разделение конфигураций по окружениям
-------------------------------------

В зависимости от обстоятельств, вам может понадобится разделить определения тестовых сервисов и боевых. К примеру, сервис оплаты не должен списывать реальные деньги с реальных счетов в процессе разработки. Поэтому стоит создать отдельный класс, имплементирующий тот же интерфейс, что и реальный класс оплаты, но не отправляющий реальных запросов, и в dev-окружении использовать именно его.

Для такого рода разделения создаются файлы `services_<env>.yaml`, к примеру, файл `services_dev.yaml` будет работать для `dev`-окружения.

Как вы можете заметить, конфигурации бандлов (пакетов) имеют похожее разделение, но уже по каталогам с именами окружений.

