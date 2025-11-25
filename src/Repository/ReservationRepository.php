<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Ouvrage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findActiveByOuvrage(Ouvrage $ouvrage): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('statuts', ['en_attente', 'disponible'])
            ->orderBy('r.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNextInQueue(Ouvrage $ouvrage): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ouvrage = :ouvrage')
            ->andWhere('r.statut = :statut')
            ->setParameter('ouvrage', $ouvrage)
            ->setParameter('statut', 'en_attente')
            ->orderBy('r.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('user', $user)
            ->setParameter('statuts', ['en_attente', 'disponible'])
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
