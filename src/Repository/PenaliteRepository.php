<?php

namespace App\Repository;

use App\Entity\Penalite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Penalite>
 */
class PenaliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Penalite::class);
    }

    public function findUnpaidByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.emprunt', 'e')
            ->andWhere('e.user = :user')
            ->andWhere('p.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'impayee')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalUnpaidByUser(User $user): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.montant)')
            ->join('p.emprunt', 'e')
            ->andWhere('e.user = :user')
            ->andWhere('p.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'impayee')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0.0;
    }
}
