<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use App\Service\Exception\StorageException;
use App\Service\Exception\WrongParameterException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ObjectRepository;

/**
 * Pizza management interface.
 */
interface PizzaManagerInterface
{
    /**
     * @return ObjectRepository|PizzaRepository
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
     * @param int         $page
     * @param int|null    $pageSize
     * @param string|null $orderBy
     * @param string|null $order
     *
     * @return Paginator
     */
    public function pager(int $page = 1, int $pageSize = null, string $orderBy = null, string $order = null): Paginator;

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
