<?php

namespace App\Repository;

use App\Entity\Ouvrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ouvrage>
 */
class OuvrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouvrage::class);
    }

    /**
     * @return Ouvrage[] Returns an array of Ouvrage objects
     */
    public function findAllSearch(Ouvrage $search): array
    {
        $query = $this->createQueryBuilder('ouvrageSearch');

        if ($search->getTitre()) {
            $query->andWhere('ouvrageSearch.titre LIKE :titre')
                ->setParameter('titre', '%'.$search->getTitre().'%');
        }
        if ($search->getIsbn()){
            $query->andWhere('ouvrageSearch.isbn = :isbn')
                ->setParameter('isbn', $search->getIsbn());
        }
        if ($search->getAnnee()){
            $query->andWhere('ouvrageSearch.annee = :annee')
                ->setParameter('annee', $search->getAnnee());
        }
        if ($search->getCategories() && count($search->getCategories()) > 0) {
            $query
                ->leftJoin('ouvrageSearch.categories', 'c')
                ->andWhere('c IN (:categories)')
                ->setParameter('categories', $search->getCategories());
        }




        return $query
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Ouvrage
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
