<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;

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

    public function __construct(PizzaManagerInterface $manager, ReceiptPartGenerateServiceInterface $receiptPartGenerateService)
    {
        $this->manager = $manager;
        $this->receiptPartGenerateService = $receiptPartGenerateService;
    }

    public function createNewPizza(string $title, string $description, int $diameter): Pizza
    {
        $pizza = $this->makePizza($title, $description, $diameter);
        //todo send event
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
