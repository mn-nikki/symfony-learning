<?php

namespace App\Repository;

use App\Entity\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    /**
     * @param int $page
     * @param int $count
     *
     * @return Paginator
     */
    public function getModelsWithParams(int $page, int $count = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('model')
            ->setFirstResult($count * ($page - 1))
            ->setMaxResults($count)
        ;

        return new Paginator($queryBuilder->getQuery());
    }
}
