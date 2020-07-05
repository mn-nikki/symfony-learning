<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use Doctrine\Persistence\ObjectRepository;

interface CarManagerInterface
{
    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository;

    /**
     * @param Model $model
     *
     * @return Model
     */
    public function update(Model $model): Model;

    /**
     * @param Model $model
     *
     * @return Model
     */
    public function delete(Model $model): Model;

    /**
     * @param Model $model
     *
     * @return Model
     */
    public function store(Model $model): Model;
}
