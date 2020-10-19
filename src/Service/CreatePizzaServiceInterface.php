<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;

interface CreatePizzaServiceInterface
{
    /**
     * @param string $title
     * @param string $description
     * @param int    $diameter
     *
     * @return Pizza
     */
    public function createNewPizza(string $title, string $description, int $diameter): Pizza;
}
