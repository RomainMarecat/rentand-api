<?php

namespace App\Repository;

use Doctrine\ORM\Query;

/**
 * MeetingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MeetingRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPreBookingMeetingsBy($criteria)
    {
        $query = $this->createQueryBuilder('entity');
        $query
            ->select('partial entity.{id, title} as meeting')
            ->addSelect('partial city.{id, title}')
            ->innerJoin('entity.city', 'city')
            ->innerJoin('entity.advert', 'advert')
            ->andWhere('advert.statut = 1')
            ->addGroupBy('city.id');

        if (isset($criteria['advert'])) {
            $query
                ->andWhere('advert.id = :advert')
                ->setParameter('advert', $criteria['advert']);
        }

        if (isset($criteria['city'])) {
            $query
                ->andWhere('city.id = :city')
                ->setParameter('city', $criteria['city']);
        }

        return $query->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}