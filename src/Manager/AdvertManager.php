<?php

namespace App\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Advert;
use Entity\AdvertTranslation;
use Helper\RegexHelper;

/**
 * Class AdvertManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class AdvertManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    protected $cities;

    protected $languages;

    protected $users;

    protected $adverts = array();

    protected $advertTranslations = array();

    public function getAdvert($id)
    {
        return $this->getEm()->getRepository('AppBundle:Advert')
            ->findOneById($id);
    }

    public function getAdvertBySlug($slug)
    {
        return $this->getEm()->getRepository('AppBundle:Advert')
            ->findOneBySlug($slug);
    }

    public function getAdvertId($slug)
    {
        $advert = $this->getEm()->getRepository('AppBundle:Advert')
            ->findOneBySlug($slug);
        if ($advert instanceof Advert and method_exists($advert, "getId")) {
            return $advert->getId();
        }
    }

    public function getAdvertByBooking($booking)
    {
        return $this->getEm()->getRepository('AppBundle:Advert')
            ->findOneByBooking($booking);
    }

    public function birthdate()
    {
        $adverts = $this->getEm()->getRepository('AppBundle:Advert')->findAll();

        foreach ($adverts as $advert) {
            $advert->getUser()->setBirthdate($advert->getBirthdate());
        }

        return true;
    }

    protected function createAdvertTranslations(array $advertTranslationV1, Advert $advert)
    {
        $advertTranslation = new AdvertTranslation();
        foreach ($advertTranslationV1 as $key => $value) {
            $setter = $this->getRegexHelper()->setCamelCase('set' . ucfirst($key));
            if (method_exists($advertTranslation, $setter)) {
                $advertTranslation->$setter(!empty($value) ? $value : null);
                if (in_array($setter, array('setCreatedAt', 'setUpdatedAt'))) {
                    $advertTranslation->$setter(new \DateTime($value));
                }
            }
        }

        $advertTranslation->setAdvert($advert);

        return $advertTranslation;
    }

    protected function registerAdvertTranslations(array $advertV1, Advert $advert)
    {
        $advertsTranslations = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT
                    a.id as advert_id,
                    a.created_at,
                    a.updated_at,
                    at.id as advert_translation_id,
                    at.title,
                    at.stripped_title as slug,
                    at.locale,
                    at.description as description1,
                    at.description2 as description2,
                    at.description3 as description3
                FROM adverttranslation at
                LEFT JOIN advert a ON at.translatable_id = a.id
                WHERE a.id = ' . $advertV1['advert_id']
            )
        );
        while ($advertsTranslations->valid()) {
            $this->getEm()->getConnection()->beginTransaction();
            try {
                $advertTranslationV1 = $advertsTranslations->current();
                $advertTranslation = $this->createAdvertTranslations($advertTranslationV1, $advert);
                // $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
                // $this->logger->info(
                //     'advert v1 / v2',
                //     array(
                //         'advertTranslationV1_old' => $advertTranslationV1,
                //         'advertTranslation' => $serializer->toArray($advertTranslation)
                //     )
                // );
                $this->getEm()->persist($advertTranslation);
                $this->getEm()->flush();
                $this->getEm()->getConnection()->commit();
                $this->addAdvertTranslations(
                    $advertTranslationV1['advert_translation_id'],
                    array('advert' => $advert->getId(), 'advert_translation_id' => $advertTranslation->getId())
                );
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.advert.insert.query.error',
                    array(
                        'advert' => $advertsTranslations->current(),
                        'message' => $e->getMessage(),
                        'message' => $e->getTraceAsString(),
                    )
                );
                throw $e;
            }
            $advertsTranslations->next();
        }

        return $this;
    }

    public function createAdvert(array $advertV1)
    {
        $advert = new Advert();
        foreach ($advertV1 as $key => $value) {
            $setter = $this->getRegexHelper()->setCamelCase('set' . ucfirst($key));
            if (method_exists($advert, $setter)) {
                if (in_array($setter, array('setBirthdate', 'setDatenaissance', 'setCreatedAt', 'setUpdatedAt'))) {
                    $advert->$setter(new \DateTime($value));
                }
            }
        }
        $advert->setTitle(0);
        $advert
            ->setFirstName($advertV1['last_name'])
            ->setLastName($advertV1['first_name'])
            ->setCancel(1)
            ->setStatut(0);

        $languagesV1 = explode(',', $advertV1['langue']);
        $languages = array();
        foreach ($languagesV1 as $language) {
            if ($this->getLanguage($language) != null) {
                $languages[] = $this->getLanguage($language);
            }
        }
        $advert->setLanguages($languages);
        $user_id = $this->getUser($advertV1['user_id']);
        $advert->setUser(
            $this->getEm()->getRepository('AppBundle:User')->findOneByEmail($advertV1['email'])
        // $this->getEm()->getRepository('AppBundle:User')->findOneById($user_id)
        );

        return $advert;
    }

    protected function addMissingAdvertTranslation($advertTranslations, Advert $advert)
    {
        if ($advertTranslations instanceof ArrayCollection) {
            $translations = $advertTranslations->map(function ($translation) {
                return $translation->getLocale();
            });
            $translations = $translations->toArray();
        } else {
            $translations = array();
        }
        $advertTranslation1 = null;
        if (!in_array('fr', $translations)) {
            $advertTranslation1 = new AdvertTranslation();
            $advertTranslation1
                ->setLocale('fr')
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setAdvert($advert);
        }
        $advertTranslation2 = null;
        if (!in_array('en', $translations)) {
            $advertTranslation2 = new AdvertTranslation();
            $advertTranslation2
                ->setLocale('en')
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setAdvert($advert);
        }

        return array($advertTranslation1, $advertTranslation2);
    }

    public function registerAdverts()
    {
        $this->setLanguages();
        $adverts = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT
                    a.id as advert_id,
                    u.id as user_id,
                    u.email as email,
                    u.last_name as last_name,
                    u.first_name as first_name,
                    a.langue as langue,
                    a.date_naissance as date_naissance,
                    u.birthdate as birthdate,
                    a.niveau as niveau,
                    a.updated_at as updated_at,
                    a.created_at as created_at
                FROM advert a
                LEFT JOIN user u ON u.id = a.user_id
                WHERE a.structure_id != 143
                ORDER BY a.id'
            )
        );
        $count = 0;
        $total = $adverts->count();
        $advertsCities = [];
        // $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        while ($adverts->valid()) {
            $this->getEm()->getConnection()->beginTransaction();
            try {
                $advertV1 = $adverts->current();
                $advert = $this->createAdvert($advertV1);
                // $this->logger->info(
                //     'advert v1 / v2',
                //     array(
                //         'advert_old' => $advertV1,
                //         'advert' => $serializer->toArray($advert)
                //     )
                // );
                $this->getEm()->persist($advert);
                $this->getEm()->flush();
                $this->getEm()->getConnection()->commit();
                $this->addAdvert($advertV1['advert_id'], $advert->getId());
                $this->registerAdvertTranslations($advertV1, $advert);
                $advertTranslations = new ArrayCollection($this->getEm()
                    ->getRepository('AppBundle:AdvertTranslation')
                    ->findByAdvert($advert));
                if ($advertTranslations == null
                    || !$advertTranslations instanceof ArrayCollection
                    || $advertTranslations->count() < 2) {
                    list($advertTranslationMissing1, $advertTranslationMissing2) =
                        $this->addMissingAdvertTranslation($advertTranslations, $advert);
                    if ($advertTranslationMissing1 != null) {
                        $this->getEm()->getConnection()->beginTransaction();
                        $this->getEm()->persist($advertTranslationMissing1);
                        $this->getEm()->flush();
                        $this->getEm()->getConnection()->commit();
                    }
                    if ($advertTranslationMissing2 != null) {
                        $this->getEm()->getConnection()->beginTransaction();
                        $this->getEm()->persist($advertTranslationMissing2);
                        $this->getEm()->flush();
                        $this->getEm()->getConnection()->commit();
                    }
                }
                $count++;
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.advert.insert.query.error',
                    array(
                        'advert' => $adverts->current(),
                        'message' => $e->getMessage(),
                        'message' => $e->getTraceAsString(),
                    )
                );
                throw $e;
            }
            $adverts->next();
        }

        $this->logger->debug('import.table.advert.insert.query.finished', array('total' => $total));

        return $this->getAdverts();
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
     * Gets the value of cities.
     *
     * @return mixed
     */
    public function getCity($key)
    {
        return isset($this->cities[$key]) ? $this->cities[$key] : null;
    }

    /**
     * Sets the value of cities.
     *
     * @param mixed $cities the cities
     *
     * @return self
     */
    protected function setCities($cities)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Gets the value of cities.
     *
     * @return mixed
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Gets the value of languages.
     *
     * @return mixed
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Gets the value of languages.
     *
     * @return mixed
     */
    public function getLanguage($key)
    {
        return isset($this->languages[$key]) ? $this->languages[$key] : null;
    }

    /**
     * Sets the value of languages.
     *
     * @param mixed $languages the languages
     *
     * @return self
     */
    protected function setLanguages($languages = null)
    {
        $languages = array();
        $languages["_1_"] = 'en';
        $languages["_2_"] = 'fr';
        $languages["_3_"] = 'es';
        $languages["_4_"] = 'it';
        $languages["_5_"] = 'de';
        $languages["_6_"] = 'ru';
        $languages["_7_"] = 'nl';
        $languages["_8_"] = 'da';
        $languages["_9_"] = 'sv';
        $languages["_10_"] = 'ar';
        $languages["_11_"] = 'el';
        $languages["_12_"] = 'hr';
        $languages["_13_"] = 'pt';
        $this->languages = $languages;

        return $this;
    }

    /**
     * Gets the value of users.
     *
     * @return mixed
     */
    public function getUser($key)
    {
        // return $this->users[$key];
        return isset($this->users[$key]) ? $this->users[$key] : null;
    }

    /**
     * Sets the value of users.
     *
     * @param mixed $users the users
     *
     * @return self
     */
    protected function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Gets the value of users.
     *
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
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
     * Sets the value of adverts.
     *
     * @param mixed $adverts the adverts
     *
     * @return self
     */
    protected function addAdvert($key, $value)
    {
        $this->adverts[$key] = $value;

        return $this;
    }

    /**
     * Sets the value of adverts.
     *
     * @param mixed $adverts the adverts
     *
     * @return self
     */
    protected function setAdverts($adverts)
    {
        $this->adverts = $adverts;

        return $this;
    }

    /**
     * Gets the value of advertTranslations.
     *
     * @return mixed
     */
    public function getAdvertTranslations()
    {
        return $this->advertTranslations;
    }

    /**
     * Sets the value of advertTranslations.
     *
     * @param mixed $advertTranslations the advert translations
     *
     * @return self
     */
    protected function addAdvertTranslations($key, $value)
    {
        $this->advertTranslations[$key] = $value;

        return $this;
    }

    /**
     * Sets the value of advertTranslations.
     *
     * @param mixed $advertTranslations the advert translations
     *
     * @return self
     */
    protected function setAdvertTranslations($advertTranslations)
    {
        $this->advertTranslations = $advertTranslations;

        return $this;
    }
}
