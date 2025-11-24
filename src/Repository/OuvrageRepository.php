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
    public function findAllSearch(Ouvrage $search, int $page = 1, int $limit = 100): array
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
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countPages(Ouvrage $search, int $limit = 100): int
    {
        $query = $this->createQueryBuilder('ouvrageSearch')
            ->select('COUNT(ouvrageSearch.id)');

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

        $total = $query->getQuery()->getSingleScalarResult();
        return (int) ceil($total / $limit);
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
