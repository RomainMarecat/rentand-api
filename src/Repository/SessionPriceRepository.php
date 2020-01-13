<?php

namespace App\Repository;

use App\Entity\SessionPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SessionPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionPrice[]    findAll()
 * @method SessionPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionPrice::class);
    }

    // /**
    //  * @return SessionPrice[] Returns an array of SessionPrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SessionPrice
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
