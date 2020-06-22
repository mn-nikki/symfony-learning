<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;
use App\Service\Exception\StorageException;
use App\Service\Exception\WrongParameterException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;

/**
 * Pizza management interface.
 */
interface PizzaManagerInterface
{
    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository;

    /**
     * Get one Pizza from storage.
     *
     * @param string|int $id
     *
     * @return Pizza|null
     */
    public function get($id): ?Pizza;

    /**
     * Find bunch of Pizzas by property.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return Collection
     *
     * @throws WrongParameterException
     */
    public function findBy(string $property, $value): Collection;

    /**
     * Update Pizza in storage.
     *
     * @param Pizza $pizza
     *
     * @return Pizza
     *
     * @throws WrongParameterException|StorageException
     */
    public function update(Pizza $pizza): Pizza;

    /**
     * Remove Pizza from storage.
     *
     * @param Pizza $pizza
     *
     * @return Pizza
     *
     * @throws StorageException
     */
    public function delete(Pizza $pizza): Pizza;

    /**
     * Store changes.
     *
     * @param Pizza $pizza
     *
     * @return Pizza
     *
     * @throws StorageException
     */
    public function store(Pizza $pizza): Pizza;
}
