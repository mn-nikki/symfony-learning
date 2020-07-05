<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Repository\ModelRepository;
use App\Service\Exception\StorageException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class CarManager implements CarManagerInterface
{
    private ModelRepository $repository;
    private EntityManagerInterface $em;

    /**
     * CarManager constructor.
     *
     * @param ModelRepository        $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(ModelRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function update(Model $model): Model
    {
        $id = $model->getId();

        if ($id === null) {
            throw new StorageException(\sprintf('Object with id = %s, was not found', $id));
        }
        $this->flushToStorage($model);
        $this->em->refresh($model);

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function delete(Model $model): Model
    {
        $id = $model->getId();

        if ($id === null) {
            throw new StorageException(\sprintf('Object with id = %s, was not found', $id));
        }
        $this->removeToStorage($model);

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function store(Model $model): Model
    {
        $id = $model->getId();

        if ($id !== null) {
            throw new StorageException(\sprintf('Object with id = %s, already exists', $id));
        }
        $this->flushToStorage($model);
        $this->em->refresh($model);

        return $model;
    }

    /**
     * @param Model $model
     */
    private function flushToStorage(Model $model): void
    {
        try {
            $this->em->persist($model);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param Model $model
     */
    private function removeToStorage(Model $model): void
    {
        try {
            $this->em->remove($model);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
