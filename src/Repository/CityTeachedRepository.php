<?php

namespace App\Repository;

use App\Entity\CityTeached;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CityTeached|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityTeached|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityTeached[]    findAll()
 * @method CityTeached[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityTeachedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityTeached::class);
    }

    // /**
    //  * @return CityTeached[] Returns an array of CityTeached objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CityTeached
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
