<?php

namespace App\Repository;

use App\Entity\Exemplaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exemplaire>
 */
class ExemplaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exemplaire::class);
    }

    public function findAllSearch(Exemplaire $search, int $page = 1, int $limit = 100): array
    {
        $query = $this->createQueryBuilder('ex');

        if ($search->getCote()) {
            $query->andWhere('ex.cote = :cote')
                ->setParameter('cote', $search->getCote());
        }

        if ($search->isDisponible() != null){
            $query->andWhere('ex.disponible = :disponible')
                ->setParameter('disponible', $search->isDisponible());
        }


        if ($search->getEtat()) {
            $query->andWhere('ex.etat = :etat')
                ->setParameter('etat', $search->getEtat());
        }

        if ($search->getOuvrage()) {
            $query->andWhere('ex.Ouvrage = :ouvrage')
                ->setParameter('ouvrage', $search->getOuvrage());
        }

        return $query
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countPages(Exemplaire $search, int $limit = 100): int
    {
        $query = $this->createQueryBuilder('ex')
            ->select('COUNT(ex.id)');

        if ($search->getCote()) {
            $query->andWhere('ex.cote = :cote')
                ->setParameter('cote', $search->getCote());
        }

        if ($search->isDisponible() != null){
            $query->andWhere('ex.disponible = :disponible')
                ->setParameter('disponible', $search->isDisponible());
        }

        if ($search->getEtat()) {
            $query->andWhere('ex.etat = :etat')
                ->setParameter('etat', $search->getEtat());
        }

        if ($search->getOuvrage()) {
            $query->andWhere('ex.Ouvrage = :ouvrage')
                ->setParameter('ouvrage', $search->getOuvrage());
        }

        $total = $query->getQuery()->getSingleScalarResult();
        return (int) ceil($total / $limit);
    }
    //    /**
    //     * @return Exemplaire[] Returns an array of Exemplaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Exemplaire
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
