<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Returns only the query (for KNP Paginator sorting)
     *
     * @return QueryBuilder
     */
    public function findAllQuery()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ;
    }


    /**
     * Returns only the query (for KNP Paginator sorting)
     *
     * @param string $sortBy
     * @param string $direction
     * @return QueryBuilder
     */
    public function findSortedQuery($sortBy, $direction)
    {
        $qb = $this->createQueryBuilder('p');
        if ($sortBy === "none" && $direction === "none") {
            return $this->findAllQuery();
        } else {
            return $qb
                ->orderBy($sortBy, $direction)
                ->getQuery()
                ;
        }

    }

    public function findAllOnSale()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where('p.isOnSale = :isOnSale')
            ->andWhere('p.quantityInStock > 0')
            ->setParameter('isOnSale', true)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function findLastsOnSale($nb)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where('p.isOnSale = :isOnSale')
            ->andWhere('p.quantityInStock > :stock')
            ->setParameter('isOnSale', true)
            ->setParameter('stock', 0)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
