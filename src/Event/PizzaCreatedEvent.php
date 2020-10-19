<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Event;

use App\Entity\Pizza;
use Symfony\Contracts\EventDispatcher\Event;

class PizzaCreatedEvent extends Event
{
    public const NAME = 'pizza.created';
    /**
     * @var Pizza
     */
    private Pizza $pizza;

    public function __construct(Pizza $pizza)
    {
        $this->pizza = $pizza;
    }

    /**
     * @return Pizza
     */
    public function getPizza(): Pizza
    {
        return $this->pizza;
    }
}
