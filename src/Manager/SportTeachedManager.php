<?php

namespace App\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Advert;
use Entity\AdvertSport;
use Entity\AdvertSportTranslation;
use Entity\Sport;
use Helper\RegexHelper;

/**
 * Class SportTeachedManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class SportTeachedManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    protected $users = array();

    protected $sports = array();

    protected $usersSportsErrors = array();

    protected $usersSports = array();

    protected $levels;

    protected $ages;

    protected $params;

    public function getAdvertSportById($userSport)
    {
        return $this->getEm()->getRepository('App:AdvertSport')->findOneById($userSport);
    }

    public function getSpecialitiesBy($userSport)
    {
        $userSport = $this->getEm()
            ->getRepository('App:AdvertSport')
            ->findSpecialitiesBy(array('advertSport' => $userSport));

        return $userSport;
    }

    public function getSportsBy($userSport)
    {
        $userSport = $this->getEm()
            ->getRepository('App:AdvertSport')
            ->findSportsBy(array('advertSport' => $userSport));

        return $userSport;
    }

    public function getAdvertSport($user, $sport)
    {
        try {
            if (!is_string($user) and !is_int($user)) {
                throw new \Exception("Undefined advert", 400);
            }

            if (!is_string($sport) and !is_int($sport)) {
                throw new \Exception("Undefined sport", 400);
            }
            $this->getLogger()->info(
                'criteria',
                array('advert' => $user, 'sport' => $sport)
            );
            $userSport = $this->getEm()
                ->getRepository('App:AdvertSport')
                ->findAdvertSport(
                    array(
                        'advert' => $user,
                        'sport' => $sport
                    )
                );

            $this->getLogger()->info(
                'advertSport',
                array('advertSport' => $userSport)
            );
        } catch (\Exception $e) {
            $this->getLogger()->error(
                "Sport api error getLevelsBySport($userSport)",
                array(
                    'message' => $e->getMessage(),
                    'line' => 'l.' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                )
            );
            $userSport = null;
        }

        return $userSport;
    }

    public function getLevelsBySport($userSport)
    {
        try {
            if (!is_string($userSport) and !is_int($userSport)) {
                throw new \Exception("Undefined advertSport", 400);
            }
            $levels = new ArrayCollection(
                $this->getEm()
                    ->getRepository('App:AdvertSport')
                    ->findLevelsBy(
                        array('advertSport' => $userSport)
                    )
            );

            $this->getLogger()->info(
                'Array of levels',
                array('levels' => $levels)
            );

            $levels['params'] = $this->getParams()->getLevels();
        } catch (\Exception $e) {
            $this->getLogger()->error(
                "Sport api error getLevelsBySport($userSport)",
                array(
                    'message' => $e->getMessage(),
                    'line' => 'l.' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                )
            );
            $levels = array();
        }

        return $levels;
    }

    public function getAgesBySport($userSport)
    {
        try {
            if (!is_string($userSport) and !is_int($userSport)) {
                throw new \Exception("Undefined advertSport", 400);
            }
            $ages = new ArrayCollection(
                $this->getEm()
                    ->getRepository('App:AdvertSport')
                    ->findAgesBy(
                        array(
                            'advertSport' => $userSport
                        )
                    )
            );

            $this->getLogger()->info(
                'ArrayCollection of ages',
                array('ages' => $ages)
            );
            $ages['params'] = $this->getParams()->getAges();
        } catch (\Exception $e) {
            $this->getLogger()->error(
                "Sport api error getAgesBySport($userSport)",
                array(
                    'message' => $e->getMessage(),
                    'line' => 'l.' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                )
            );
            $ages = array();
        }

        return $ages;
    }

    protected function findSport(array $usersSportV1)
    {
        $sportId = $usersSportV1['sport_id'];
        // if ($usersSportV1['level'] >= 2) {
        //     $sportId = $usersSportV1['parent_id'];
        // }
        if (!$this->getSport($sportId)) {
            return false;
        }
        $slug = $this->getSport($sportId);
        $sport = $this->getEm()->getRepository('App:Sport')->findOneBySlug($slug);
        if (!$sport instanceof Sport) {
            return false;
        }

        return $sport;
    }

    protected function findAdvert(array $usersSportV1)
    {
        $userId = $this->getAdvert($usersSportV1['advert_id']);
        $user = $this->getEm()->getRepository('App:Advert')->findOneById($userId);
        if (!$user instanceof Advert) {
            return false;
        }

        return $user;
    }

    protected function createAdvertsSport(array $usersSportV1)
    {
        $usersSport = new AdvertSport();
        $agesV1 = explode(',', $usersSportV1['ages']);
        $levelsV1 = explode(',', $usersSportV1['levels']);
        $ages = array();
        foreach ($agesV1 as $age) {
            if ($this->getAge($age) != null) {
                $ages[] = $this->getAge($age);
            }
        }
        $levels = array();
        foreach ($levelsV1 as $level) {
            if ($this->getLevel($level) != null) {
                $levels[] = $this->getLevel($level);
            }
        }

        $user = $this->findAdvert($usersSportV1);
        $sport = $this->findSport($usersSportV1);

        // if ($sport instanceof Sport) {
        //     $user->getSports();
        // }

        if (!$sport || !$user) {
            $this->addAdvertsSportsErrors(
                array(
                    'sport_id_v1' => $usersSportV1['sport_id'],
                    'advert_id_v1' => $usersSportV1['advert_id'],
                    'sport_slug_v2' => $this->getSport($usersSportV1['sport_id']),
                    'sport_id_v2' => $sport instanceof Sport ? $sport->getId() : $sport,
                    'advert_id_v2' => $user instanceof Advert ? $user->getId() : $user,
                )
            );

            return false;
        }

        $usersSport
            ->setOrderNumber($sport->getLevel())
            ->setLevels($levels)
            ->setAges($ages)
            ->setAdvert($user)
            ->setSport($sport);

        return $usersSport;
    }

    public function registerAdvertsSports(array $users, array $sports)
    {
        $this->setAdverts($users);
        $sports = json_decode('{
          "38": "ski",
          "39": "snowboard",
          "72": "telemark",
          "40": "nordic-ski",
          "63": "alpinism",
          "107": "running-1",
          "89": "physical-training",
          "56": "fitness",
          "57": "gym",
          "27": "fighting-sports",
          "51": "golf",
          "65": "horse-riding",
          "58": "dancing",
          "25": "bmx",
          "101": "fatbike",
          "23": "mountain-bike",
          "74": "backcountry-freeride",
          "77": "freestyle",
          "79": "skicross",
          "75": "hiking-ski",
          "111": "biathlon",
          "41": "snowshoes",
          "66": "sled",
          "73": "icefall",
          "42": "ice-driving",
          "62": "climbing",
          "92": "via-ferrata",
          "53": "paragliding",
          "61": "hiking",
          "81": "trail",
          "82": "orienteering-race",
          "88": "paddle",
          "11": "rowing",
          "17": "rafting",
          "13": "canoe",
          "15": "water-ski",
          "10": "surf",
          "19": "scuba-diving",
          "18": "canyoning",
          "108": "athletism",
          "67": "fitness-programme",
          "175": "aquagym",
          "93": "pilates",
          "84": "yoga",
          "28": "boxing",
          "30": "judo",
          "34": "karate",
          "33": "taekwondo",
          "21": "badminton",
          "20": "ping-pong",
          "22": "squash",
          "12": "tennis"
        }', true);

        $this->setSports($sports);
        $this->setLevels();
        $this->setAges();

        $usersSports = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT
                    ac.advert_id as advert_id,
                    ac.category_id as sport_id,
                    c.parent_id,
                    c.level,
                    u.id as user_id,
                    a.structure_id as structure_id,
                    a.langue as languages,
                    a.niveau as levels,
                    a.age_eleve as ages,
                    a.updated_at as updated_at,
                    a.created_at as created_at,
                    a.handi
                FROM advert_category ac
                LEFT JOIN category c ON ac.category_id = c.id
                LEFT JOIN advert a ON ac.advert_id = a.id
                LEFT JOIN user u ON u.id = a.user_id
                WHERE a.structure_id != 143'
            )
        );
        // $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        while ($usersSports->valid()) {
            $this->getEm()->getConnection()->beginTransaction();
            try {
                $usersSportV1 = $usersSports->current();
                $usersSport = $this->createAdvertsSport($usersSportV1);
                if ($usersSport instanceof AdvertSport) {
                    // $this->logger->info(
                    //     'advert v1 / v2',
                    //     array(
                    //         'advertsSportV1_old' => $usersSportV1,
                    //         'advertsSport' => $serializer->toArray($usersSport)
                    //     )
                    // );
                    $this->getEm()->persist($usersSport);
                    $this->getEm()->flush();
                    $this->getEm()->getConnection()->commit();

                    if ($usersSportV1['handi']) {
                        foreach (array('fr', 'en') as $value) {
                            $this->getEm()->getConnection()->beginTransaction();
                            $userSportTranslation = new AdvertSportTranslation();
                            $userSportTranslation
                                ->setLocale($value)
                                ->setHandiSport('oui')
                                ->setCreatedAt(new \DateTime($usersSportV1['created_at']))
                                ->setUpdatedAt(new \DateTime($usersSportV1['updated_at']))
                                ->setAdvertSport($usersSport);
                            $this->getEm()->persist($userSportTranslation);
                            $this->getEm()->flush();
                            $this->getEm()->getConnection()->commit();
                        }
                    }
                } else {
                    $this->getEm()->getConnection()->commit();
                }
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.advert.insert.query.error',
                    array(
                        'message' => $e->getMessage(),
                        'message' => $e->getTraceAsString(),
                    )
                );
                throw $e;
            }
            $usersSports->next();
        }
        $this->getLogger()->info('Sports', $this->getSports());

        $this->registerSpecialitesSports();

        return $this->getAdvertsSportsErrors();
    }

    protected function createSpecialitiesSports(AdvertSport $usersSport)
    {
        $this->getEm()->getConnection()->beginTransaction();
        $sport = $usersSport->getSport();
        $b = false;
        if ($sport instanceof Sport) {
            if ($sport->getLevel() === 2) {
                $this->getLogger()->info(
                    'sport level = 2',
                    array(
                        'sport_id' => $sport->getId()
                    )
                );
                $parentAdvertSport = $this->getEm()->getRepository('App:AdvertSport')
                    ->findOneByCriteria(
                        array(
                            'advert' => $usersSport->getAdvert(),
                            'sport' => $sport->getParent()
                        )
                    );
                if (!$parentAdvertSport) {
                    $this->getLogger()->info(
                        'parent not found, create new AdvertSport',
                        array(
                            'sport_id' => $sport->getParent()->getId(),
                            'advert_id' => $usersSport->getAdvert()->getId()
                        )
                    );
                    $parentAdvertSport = new AdvertSport();
                    $parentAdvertSport
                        ->setOrderNumber($sport->getParent()->getLevel())
                        ->setLevels($usersSport->getLevels())
                        ->setAges($usersSport->getAges())
                        ->setAdvert($usersSport->getAdvert())
                        ->setSport($sport->getParent());
                    $b = true;
                }

                $this->getLogger()->info(
                    'parent and sport flushed',
                    array(
                        'sport_id' => $sport->getParent()->getId(),
                        'advert_id' => $usersSport->getAdvert()->getId()
                    )
                );

                if ($b) {
                    foreach (array('fr', 'en') as $value) {
                        $userSportTranslation = new AdvertSportTranslation();
                        $userSportTranslation
                            ->setLocale($value)
                            ->setHandiSport('oui')
                            ->setCreatedAt(new \DateTime())
                            ->setUpdatedAt(new \DateTime())
                            ->setAdvertSport($parentAdvertSport);
                    }
                }

                $parentAdvertSport->addSpeciality($sport);
                $sport->addAdvertSport($parentAdvertSport);
                $this->getEm()->persist($parentAdvertSport);
                $this->getEm()->flush();
            }
        }
        $this->getEm()->getConnection()->commit();

        return $this;
    }

    protected function registerSpecialitesSports()
    {
        $usersSports = new \ArrayIterator(
            $this->getEm()->getRepository('App:AdvertSport')->findAll()
        );
        while ($usersSports->valid()) {
            try {
                $usersSport = $usersSports->current();
                $this->createSpecialitiesSports($usersSport);
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.advert.insert.query.error',
                    array(
                        'message' => $e->getMessage(),
                        'message' => $e->getTraceAsString(),
                    )
                );
                throw $e;
            }
            $usersSports->next();
        }
        $this->getEm()->getConnection()->beginTransaction();
        $this->getEm()->getConnection()
            ->executeQuery('DELETE FROM advert_sport WHERE orderNumber = 2');
        $this->getEm()->getConnection()->commit();

        return $this;
    }

    /**
     * Gets the value of em.
     *
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Sets the value of em.
     *
     * @param mixed $em the em
     *
     * @return self
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * Gets the value of connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the value of connection.
     *
     * @param mixed $connection the connection
     *
     * @return self
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Gets the value of logger.
     *
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the value of logger.
     *
     * @param mixed $logger the logger
     *
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Gets the value of regexHelper.
     *
     * @return mixed
     */
    public function getRegexHelper()
    {
        return $this->regexHelper;
    }

    /**
     * Sets the value of regexHelper.
     *
     * @param mixed $regexHelper the regex helper
     *
     * @return self
     */
    public function setRegexHelper(RegexHelper $regexHelper)
    {
        $this->regexHelper = $regexHelper;

        return $this;
    }

    /**
     * Gets the value of adverts.
     *
     * @return mixed
     */
    public function getAdverts()
    {
        return $this->adverts;
    }

    /**
     * Gets the value of advert.
     *
     * @return mixed
     */
    public function getAdvert($key)
    {
        return isset($this->adverts[$key]) ? $this->adverts[$key] : null;
    }

    /**
     * Sets the value of adverts.
     *
     * @param mixed $users the adverts
     *
     * @return self
     */
    protected function setAdverts($users)
    {
        $this->adverts = $users;

        return $this;
    }

    /**
     * Gets the value of sports.
     *
     * @return mixed
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * Gets the value of sport.
     *
     * @return mixed
     */
    public function getSport($key)
    {
        return isset($this->sports[$key]) ? $this->sports[$key] : null;
    }

    /**
     * Sets the value of sports.
     *
     * @param mixed $sports the sports
     *
     * @return self
     */
    protected function setSports($sports)
    {
        $this->sports = $sports;

        return $this;
    }

    /**
     * Gets the value of advertsSportsErrors.
     *
     * @return mixed
     */
    public function getAdvertsSportsErrors()
    {
        return $this->advertsSportsErrors;
    }

    /**
     * Sets the value of advertsSportsErrors.
     *
     * @param mixed $usersSportsErrors the adverts sports errors
     *
     * @return self
     */
    protected function addAdvertsSportsErrors($value)
    {
        $this->advertsSportsErrors[] = $value;

        return $this;
    }

    /**
     * Sets the value of advertsSportsErrors.
     *
     * @param mixed $usersSportsErrors the adverts sports errors
     *
     * @return self
     */
    protected function setAdvertsSportsErrors($usersSportsErrors)
    {
        $this->advertsSportsErrors = $usersSportsErrors;

        return $this;
    }

    /**
     * Gets the value of advertsSports.
     *
     * @return mixed
     */
    public function getAdvertsSports()
    {
        return $this->advertsSports;
    }

    /**
     * Sets the value of advertsSports.
     *
     * @param mixed $usersSports the adverts sports
     *
     * @return self
     */
    protected function setAdvertsSports($usersSports)
    {
        $this->advertsSports = $usersSports;

        return $this;
    }

    /**
     * Gets the value of levels.
     *
     * @return mixed
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Gets the value of level.
     *
     * @return mixed
     */
    public function getLevel($key)
    {
        return isset($this->levels[$key]) ? $this->levels[$key] : null;
    }

    /**
     * Sets the value of levels.
     *
     * @param mixed $levels the levels
     *
     * @return self
     */
    protected function setLevels($levels = null)
    {
        $levels = array();
        $levels["_1_"] = 1;
        $levels["_2_"] = 2;
        $levels["_3_"] = 3;
        $levels["_4_"] = 4;
        $this->levels = $levels;

        return $this;
    }

    /**
     * Gets the value of ages.
     *
     * @return mixed
     */
    public function getAges()
    {
        return $this->ages;
    }

    /**
     * Gets the value of age.
     *
     * @return mixed
     */
    public function getAge($key)
    {
        return isset($this->ages[$key]) ? $this->ages[$key] : null;
    }

    /**
     * Sets the value of ages.
     *
     * @param mixed $ages the ages
     *
     * @return self
     */
    protected function setAges($ages = null)
    {
        $ages = array();
        $ages["_1_"] = 1;
        $ages["_2_"] = 2;
        $ages["_3_"] = 3;
        $ages["_4_"] = 4;
        $ages["_5_"] = 5;
        $this->ages = $ages;

        return $this;
    }

    /**
     * Gets the value of params.
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets the value of params.
     *
     * @param mixed $params the params
     *
     * @return self
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}
