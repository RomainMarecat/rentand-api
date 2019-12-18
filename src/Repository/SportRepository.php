<?php

namespace App\Repository;

use Doctrine\ORM\Query;

/**
 * SportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SportRepository extends AbstractEntityRepository
{
    public function findSportByIds(array $sports)
    {
        $query = $this->createQueryBuilder('entity');
        $query
            ->select('partial entity.{id}')
            ->addSelect('partial st.{id, title, locale}')
            ->leftJoin('entity.translations', 'st', null, null, 'st.locale')
            ->andWhere('entity.id IN (:sport)')
            ->setParameter('sport', $sports);

        return $query->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }


    public function findSportsByLevel($level)
    {
        $query = $this->createQueryBuilder('entity');
        $query
            ->select('partial entity.{id}')
            ->addSelect('partial ts.{id, title, locale}')
            ->leftJoin('entity.advert', 'ads')
            ->leftJoin('entity.translations', 'ts', 'WITH', 'ts.sport = entity.id', 'ts.locale')
            ->andWhere('entity.level =:level')
            ->setParameter('level', $level)
            ->addGroupBy('entity.id')
            ->orderBy('entity.createdAt', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function findSportsByParentFamily($family)
    {
        $query = $this->createQueryBuilder('entity');
        $query
            ->select('partial entity.{id}')
            ->addSelect('partial ts.{id, title, locale}')
            ->leftJoin('entity.families', 'f')
            ->leftJoin('entity.advert', 'ads')
            ->leftJoin('entity.translations', 'ts', 'WITH', 'ts.sport = entity.id', 'ts.locale')
            ->andWhere('f.id = :family')
            // ->andWhere('entity.parent IS NULL')
            ->setParameter('family', $family)
            ->addGroupBy('entity.id')
            // ->having($query->expr()->count('ads.id') . ' > 0')
            ->orderBy('entity.createdAt')
            ->setMaxResults(9);

        return $query->getQuery()->getResult();
    }

    public function findPreBookingBy(array $criteria)
    {
        $query = $this->createQueryBuilder('entity');
        $query
            ->select('entity')
            ->addSelect('ts')
            ->leftJoin('entity.advert', 'ads')
            ->leftJoin('entity.translations', 'ts', 'WITH', 'ts.sport = entity.id', 'ts.locale')
            ->leftJoin('ads.advert', 'advert')
            ->andWhere('advert.statut = 1');

        if (isset($criteria['advert'])) {
            $query
                ->andWhere('advert.id = :advert')
                ->setParameter('advert', $criteria['advert']);
        }

        if (isset($criteria['sport'])) {
            $query
                ->andWhere('entity.id = :sport')
                ->setParameter('sport', $criteria['sport']);
        }

        return $query->getQuery()->getResult();
    }

    public function findPreBookingSpecialitiesBy(array $criteria)
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare(
            sprintf(
                "SELECT ss.sport_id as id, st.*, adst.handisport
                FROM specialities_sports spe
                INNER JOIN sport_sport ss ON ss.sport_id = spe.sport_id
                LEFT JOIN sport_translation st ON st.sport_id = ss.sport_id
                INNER JOIN advert_sport ads ON ads.advert_sport_id = spe.advert_sport_id
                INNER JOIN advert_sport_translation adst ON adst.advert_sport_id = ads.advert_sport_id
                INNER JOIN sport_sport adss ON adss.sport_id = ads.sport_id
                INNER JOIN advert_advert ad ON ad.advert_id = ads.advert_id
                WHERE adss.sport_id = %s AND ad.advert_id = %s
                ",
                $connection->quote($criteria['sport']),
                $connection->quote($criteria['advert'])
            )
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    public function findSpecialitiesBySport($sport)
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare(
            sprintf(
                "SELECT ss.sport_id as id, st.title, st.locale, adst.handisport
                FROM specialities_sports spe
                INNER JOIN sport_sport ss ON ss.sport_id = spe.sport_id
                INNER JOIN sport_translation st ON st.sport_id = ss.sport_id
                INNER JOIN advert_sport ads ON ads.advert_sport_id = spe.advert_sport_id
                INNER JOIN advert_sport_translation adst ON adst.advert_sport_id = ads.advert_sport_id
                INNER JOIN sport_sport adss ON adss.sport_id = ads.sport_id
                INNER JOIN advert_advert ad ON ad.advert_id = ads.advert_id
                WHERE adss.sport_id = %s
                ",
                $connection->quote($sport)
            )
        );
        $statement->execute();
        return $statement->fetchAll();
    }
}