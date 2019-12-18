<?php

namespace App\Repository;

use App\Entity\UserMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMetadata[]    findAll()
 * @method UserMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMetadata::class);
    }

    // /**
    //  * @return UserMetadata[] Returns an array of UserMetadata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMetadata
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
