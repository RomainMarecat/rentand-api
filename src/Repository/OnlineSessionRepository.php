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
        $qb = $this->createQueryBuilder('o');

        if ($onlineSession->getCityTeached()) {
            $qb->andWhere('o.cityTeached = :cityTeached')
                ->setParameter('cityTeached', $onlineSession->getCityTeached());
        }
        if ($onlineSession->getSportTeached()) {
            $qb->andWhere('o.sportTeached = :sportTeached')
                ->setParameter('sportTeached', $onlineSession->getSportTeached());
        }
        if ($onlineSession->getUser()) {
            $qb->andWhere('o.user = :user')
                ->setParameter('user', $onlineSession->getUser());
        }
        if ($onlineSession->getStartDate()) {
            $qb->andWhere('o.startDate = :startDate')
                ->setParameter('startDate', $onlineSession->getStartDate());
        }
        if ($onlineSession->getEndDate()) {
            $qb->andWhere('o.endDate = :endDate')
                ->setParameter('endDate', $onlineSession->getEndDate());
        }
        return $qb
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
