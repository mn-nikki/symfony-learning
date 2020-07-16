<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Model;
use App\Repository\ModelRepository;
use App\Service\Exception\StorageException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;

class CarManager implements CarManagerInterface
{
    private ModelRepository $repository;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    /**
     * CarManager constructor.
     *
     * @param ModelRepository        $repository
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $logger
     */
    public function __construct(ModelRepository $repository, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->logger = $logger;
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
            $message = \sprintf('Object with id = %s, was not found', $id);

            $this->logger->critical($message);
            throw new StorageException($message);
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
            $message = \sprintf('Object with id = %s, was not found', $id);

            $this->logger->critical($message);
            throw new StorageException($message);
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
            $message = \sprintf('Object with id = %s, already exists', $id);

            $this->logger->critical($message);
            throw new StorageException($message);
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
            $this->logger->critical($e->getMessage());
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
            $this->logger->critical($e->getMessage());
            throw new StorageException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
