<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use App\Service\Exception\StorageException;
use App\Service\Exception\WrongParameterException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;

/**
 * Pizza management.
 */
class PizzaManager implements PizzaManagerInterface
{
    private EntityManagerInterface $em;
    private PizzaRepository $repository;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, PizzaRepository $repository, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @return ObjectRepository|PizzaRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function get($id): ?Pizza
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findBy(string $property, $value): Collection
    {
        $this->logger->info('Attempt to find Pizza', [
            'parameter' => $property,
            'value' => $this->getValueName($value),
        ]);

        if (!\array_key_exists($property, \array_flip($this->getProperties()))) {
            throw new WrongParameterException(\sprintf('Property \'%s\' not exists in \'%s\'', $property, Pizza::class));
        }

        $result = $this->repository->findBy([$property => $value]);

        return new ArrayCollection($result);
    }

    /**
     * @inheritDoc
     */
    public function update(Pizza $pizza): Pizza
    {
        $this->logger->info('Attempt to update Pizza', [
            'id' => $pizza->getId(),
        ]);

        if ($pizza->getId() === null) {
            throw new StorageException('You are trying to update not existence item. Use \'store\' method');
        }
        $this->flushToStorage($pizza);
        $this->em->refresh($pizza);

        return $pizza;
    }

    /**
     * @inheritDoc
     */
    public function delete(Pizza $pizza): Pizza
    {
        $this->logger->info('Attempt to delete Pizza', [
            'id' => $pizza->getId(),
        ]);

        try {
            $this->em->remove($pizza);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage(), (int) $e->getCode(), $e);
        }

        return $pizza;
    }

    /**
     * @inheritDoc
     */
    public function store(Pizza $pizza): Pizza
    {
        $this->logger->info('Attempt to store new Pizza');

        if ($pizza->getId() !== null) {
            throw new StorageException('You are trying to store existence item as new. Use \'update\' method');
        }
        $this->flushToStorage($pizza);
        $this->em->refresh($pizza);

        $this->logger->info('New Pizza stored', ['id' => $pizza->getId()]);

        return $pizza;
    }

    /**
     * Get name of any object.
     *
     * @param $value
     *
     * @return string
     */
    private function getValueName($value): string
    {
        if (\is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value)) {
            return \method_exists($value, '__toString') ? (string) $value : \get_class($value);
        }

        return 'Unknown';
    }

    /**
     * Get all property names.
     *
     * @return array
     */
    private function getProperties(): array
    {
        $properties = (new \ReflectionClass(Pizza::class))
            ->getProperties(\ReflectionProperty::IS_PRIVATE);

        return \array_map(fn (\ReflectionProperty $property) => $property->getName(), $properties);
    }

    /**
     * Store item.
     *
     * @param Pizza $pizza
     */
    private function flushToStorage(Pizza $pizza): void
    {
        try {
            $this->em->persist($pizza);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
