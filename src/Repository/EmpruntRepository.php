<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->setParameter('user', $user)
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findEmpruntsEnRetard(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRetourPrevue < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findEmpruntsNeedingReminderJ3(): array
    {
        $date = new \DateTime('+3 days');
        $dateStart = (clone $date)->setTime(0, 0);
        $dateEnd = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRetourPrevue BETWEEN :start AND :end')
            ->andWhere('e.dateRappelJ3 IS NULL')
            ->setParameter('start', $dateStart)
            ->setParameter('end', $dateEnd)
            ->getQuery()
            ->getResult();
    }

    public function findEmpruntsNeedingReminderJ0(): array
    {
        $today = new \DateTime();
        $dateStart = (clone $today)->setTime(0, 0);
        $dateEnd = (clone $today)->setTime(23, 59, 59);

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRetourPrevue BETWEEN :start AND :end')
            ->andWhere('e.dateRappelJ0 IS NULL')
            ->setParameter('start', $dateStart)
            ->setParameter('end', $dateEnd)
            ->getQuery()
            ->getResult();
    }

    public function findEmpruntsNeedingReminderJ7(): array
    {
        $date = new \DateTime('-7 days');
        $dateStart = (clone $date)->setTime(0, 0);
        $dateEnd = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRetourPrevue BETWEEN :start AND :end')
            ->andWhere('e.dateRappelJ7 IS NULL')
            ->setParameter('start', $dateStart)
            ->setParameter('end', $dateEnd)
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le délai moyen d'emprunt en jours
     */
    public function getAverageLoanDuration(): int
    {
        $empruntsTermines = $this->createQueryBuilder('e')
            ->andWhere('e.dateRetourEffective IS NOT NULL')
            ->andWhere('e.statut = :statut')
            ->setParameter('statut', 'termine')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult();

        if (empty($empruntsTermines)) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($empruntsTermines as $emprunt) {
            $diff = $emprunt->getDateEmprunt()->diff($emprunt->getDateRetourEffective());
            $totalDays += $diff->days;
            $count++;
        }

        return $count > 0 ? (int) round($totalDays / $count) : 0;
    }
}
