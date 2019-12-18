<?php

namespace App\Repository;

use App\Entity\MeetingPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MeetingPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingPoint[]    findAll()
 * @method MeetingPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingPoint::class);
    }

    // /**
    //  * @return MeetingPoint[] Returns an array of MeetingPoint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeetingPoint
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
