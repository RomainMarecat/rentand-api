<?php

namespace App\Repository;

use App\Entity\AppMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AppMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppMetadata[]    findAll()
 * @method AppMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppMetadata::class);
    }

    // /**
    //  * @return AppMetadata[] Returns an array of AppMetadata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AppMetadata
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
