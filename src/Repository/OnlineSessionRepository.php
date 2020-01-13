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

    /**
     * @param OnlineSession $onlineSession
     * @return OnlineSession[] Returns an array of OnlineSession objects
     */
    public function findByCriteria(OnlineSession $onlineSession)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.cityTeached = :cityTeached')
            ->setParameter('cityTeached', $onlineSession->getCityTeached())
            ->andWhere('o.sportTeached = :sportTeached')
            ->setParameter('sportTeached', $onlineSession->getSportTeached())
            ->andWhere('o.user = :user')
            ->setParameter('user', $onlineSession->getUser())
            ->andWhere('o.timeRange = :timeRange')
            ->setParameter('timeRange', $onlineSession->getTimeRange())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
