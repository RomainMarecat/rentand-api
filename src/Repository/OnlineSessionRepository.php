<?php

namespace App\Repository;

use App\Entity\OnlineSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OnlineSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method OnlineSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method OnlineSession[]    findAll()
 * @method OnlineSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OnlineSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnlineSession::class);
    }

    // /**
    //  * @return OnlineSession[] Returns an array of OnlineSession objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OnlineSession
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
