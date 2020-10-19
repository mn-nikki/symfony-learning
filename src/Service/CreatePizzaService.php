<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;
use App\Event\PizzaCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreatePizzaService implements CreatePizzaServiceInterface
{
    /**
     * @var PizzaManagerInterface
     */
    private PizzaManagerInterface $manager;
    /**
     * @var ReceiptPartGenerateServiceInterface
     */
    private ReceiptPartGenerateServiceInterface $receiptPartGenerateService;

    /**
     * @var array|string[]
     */
    protected static array $food = [
        'Олливки',
        'Маслины',
        'Ананасы',
        'Бекон',
        'Салями',
        'Грибы',
        'Курица',
        'Соленые огурцы',
        'Помидоры',
        'Сыр',
    ];
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    /**
     * CreatePizzaService constructor.
     *
     * @param EventDispatcherInterface            $dispatcher
     * @param PizzaManagerInterface               $manager
     * @param ReceiptPartGenerateServiceInterface $receiptPartGenerateService
     */
    public function __construct(EventDispatcherInterface $dispatcher, PizzaManagerInterface $manager, ReceiptPartGenerateServiceInterface $receiptPartGenerateService)
    {
        $this->manager = $manager;
        $this->receiptPartGenerateService = $receiptPartGenerateService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $title
     * @param string $description
     * @param int    $diameter
     *
     * @return Pizza
     *
     * @throws \Exception
     */
    public function createNewPizza(string $title, string $description, int $diameter): Pizza
    {
        /*
         * Создаем пиццу
         */
        $pizza = $this->makePizza($title, $description, $diameter);
        /*
         * Посылаем событие
         */
        $this->dispatcher->dispatch(new PizzaCreatedEvent($pizza), PizzaCreatedEvent::NAME);

        return $pizza;
    }

    /**
     * @param string $title
     * @param string $description
     * @param int    $diameter
     *
     * @return Pizza
     *
     * @throws \Exception
     */
    protected function makePizza(string $title, string $description, int $diameter)
    {
        $pizza = new Pizza();
        $pizza->setTitle($title);
        $pizza->setDescription($description);
        $pizza->setDiameter($diameter);
        $pizza->addPart($this->receiptPartGenerateService->generate(static::$food[random_int(0, 9)]));

        return $this->manager->store($pizza);
    }
}
