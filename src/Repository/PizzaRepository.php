<?php

namespace App\Repository;

use App\Entity\Pizza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pizza|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pizza|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pizza[]    findAll()
 * @method Pizza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PizzaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pizza::class);
    }

    public function withNIngredients(int $n): Collection
    {
        $queryBuilder = $this->createQueryBuilder('pizza')
            ->join('pizza.parts', 'parts');
        $queryBuilder->having($queryBuilder->expr()->eq($queryBuilder->expr()->count('parts.id'), ':count'))
            ->setParameter('count', $n, ParameterType::INTEGER)
            ->groupBy('pizza.id')
        ;

        return new ArrayCollection($queryBuilder->getQuery()->getResult() ?? []);
    }
}
