<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByPretixId(int $pretixId): ?Product
    {
        return $this->createQueryBuilder('p')
                    ->where('p.pretixId = :pretixId')
                    ->setParameter('pretixId', $pretixId)
                    ->getQuery()
                    ->execute()[0] ?? null;
    }
}
