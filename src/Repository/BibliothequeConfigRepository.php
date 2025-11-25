<?php

namespace App\Repository;

use App\Entity\BibliothequeConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BibliothequeConfig>
 */
class BibliothequeConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BibliothequeConfig::class);
    }

    public function findOneByCle(string $cle): ?BibliothequeConfig
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.cle = :cle')
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
