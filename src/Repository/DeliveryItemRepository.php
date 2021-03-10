<?php

namespace App\Repository;

use App\Entity\DeliveryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeliveryItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryItem[]    findAll()
 * @method DeliveryItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryItem::class);
    }

    // /**
    //  * @return DeliveryItem[] Returns an array of DeliveryItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeliveryItem
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
