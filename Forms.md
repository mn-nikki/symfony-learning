Формы (Symfony forms), валидатор
================================

[Статьи официальной документации](https://symfony.com/doc/current/forms.html)

Формы — одна из самых мощных (но в то же время сложных) частей как Symfony-фреймворка, так и веб-разработки в целом. При современном (микросервисном) подходе формы скорее не нужны, вы должны согласовывать данные по-другому, но в традиционной парадигме вы должны:
- определить правила рендеринга формы (непосредственно html);
- проверить полученные данные;
- присвоить полученные данные полям объекта;
- что-то, в конце концов, сделать с этим объектом.

Symfony предлагает следующий workflow:
- **построение формы** в контроллере или специальном классе;
- **рендеринг формы** в twig-шаблоне;
- **процессинг данных** формы, включающий в себя валидацию и преобразование в данные объекта.

Чтобы использовать все возможности symfony forms, нужны компоненты `symfony/form` и `symfony/validator`

```shell
composer require form validator
```

Построение форм
---------------

Теоретически сделать форму прямо в контроллере можно c помощью метода `createFormBuilder` из абстрактного контроллера, но это плохая практика: нет достаточной гибкости и размывается ответственность. Следует делать формы в специальных классах:

```php
<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\Pizza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Main Pizza form.
 */
class PizzaType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('diameter')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pizza::class,
        ]);
    }
}
```

Это **самый** простой пример формы.

Формы могут быть гораздо более сложными. Например, нормальной ситуацией является вложенная коллекция (то есть, на уровне сущностей БД, связь «Один-ко-многим»), или селект (выпадающий список) со связью «многие-к-одному».

Смотрите примеры в классах `App\Form\SimplePizzaType`, `App\Form\PizzaType` и `App\Form\ReceiptPartType`.

Валидация
---------

[Статья официальной документации](https://symfony.com/doc/current/validation.html)

Валидация — одна из самых распространенных задач веб-разработки. И если без форм в современном окружении мы можем обойтись, то без валидации данных — нет, данные всё равно надо проверять. Symfony validator — компонент, который проверяет данные по определенным правилам.

В большинстве случаев валидация используется для сущностей БД (но это не значит, что она может использоваться только так). Constraints (ограничения) задаются аннотациями:

```php
use Symfony\Component\Validator\Constraints as Assert;

class Pizza
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    private ?string $title = null;
}
```

В результате валидации нашей пиццы будет включена проверка, что `title` не пустой и его длина не меньше 6 символов. 

Обратите внимание — в результате проверки **формы**, то есть этот валидатор не включится просто так, и если вы где-то программно создаёте свой объект, вы должны убедиться, что в нём всё правильно. Сделать это легко — валидатор можно использовать как сервис.

Для примера можно посмотреть команду `App\Command\PizzaValidationCommand`.

В формы также можно добавлять валидаторы — если, предположим, поле формы не относится к какому-то свойству сущности, или же вся форма ориентируется не на какой-то класс.

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('someField', TextType::class, [
            'required' => true,
            'constraints' => [new Length(['min' => 6])]
        ])
    ;
}
```
