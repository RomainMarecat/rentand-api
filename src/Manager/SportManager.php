<?php

namespace App\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Sport;
use Entity\SportTranslation;
use Helper\RegexHelper;

/**
 * Class SportManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class SportManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    public function get($sport)
    {
        return $this->getEm()
            ->getRepository('AppBundle:Sport')
            ->findOneById($sport);
    }

    public function getSpecialities($sport, $advert)
    {
        try {
            if (!is_string($sport) and !is_int($sport)) {
                throw new \Exception("Undefined sport", 400);
            }

            if (!is_string($advert) and !is_int($advert)) {
                throw new \Exception("Undefined sport", 400);
            }
            $specialities = new ArrayCollection($this->getEm()
                ->getRepository('AppBundle:Sport')
                ->findPreBookingSpecialitiesBy(array('sport' => $sport, 'advert' => $advert)));

            $this->getLogger()->info(
                'specialities',
                array('specialities' => $specialities)
            );
        } catch (\Exception $e) {
            $this->getLogger()->error(
                "Sport api error getSpecialities($sport)",
                array(
                    'message' => $e->getMessage(),
                    'line' => 'l.' . $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                )
            );
            $specialities = array();
        }

        return $specialities;
    }

    public function registerSports()
    {
        $categories = $this->getCats();
        $sports = $this->getSports();
        $specialities = $this->getSpecs();

        $listIdSlug = [];

        $this->getEm()->getConnection()->beginTransaction();

        try {
            $newCats = [];
            foreach ($categories as $oldId => $categoryOld) {
                $category = new sport;
                $category->setLevel(0);
                foreach ($categoryOld['translations'] as $locale => $trans) {
                    $translation = new SportTranslation;
                    $translation->setSport($category);
                    $translation->setlocale($locale);
                    $translation->setTitle($trans);

                    $category->addTranslation($translation);
                    if ($locale === 'en') {
                        if ($trans == '') {
                            $this->logger->warning($oldId);
                        }
                        $category->setName($trans);
                    }
                }

                $this->getEm()->persist($category);
                $this->getEm()->flush();
                $newCats[$category->getId()] = $oldId;

                $listIdSlug[$oldId] = $category->getSlug();
            }

            $newSports = [];
            foreach ($sports as $oldId => $sportOld) {
                $sport = new sport;
                $sport->setLevel(1);

                $newId = array_search($sportOld['parent'], $newCats);
                $parent = $this->getEm()->getRepository('AppBundle:Sport')->find($newId);
                $sport->setParent($parent);
                $parent->addChild($sport);

                foreach ($sportOld['translations'] as $locale => $trans) {
                    $translation = new SportTranslation;
                    $translation->setSport($sport);
                    $translation->setlocale($locale);
                    $translation->setTitle($trans);

                    $sport->addTranslation($translation);
                    if ($locale === 'en') {
                        if ($trans == '') {
                            $this->logger->warning($oldId);
                        }
                        $sport->setName($trans);
                    }
                }
                $this->getEm()->persist($sport);
                $this->getEm()->flush();
                $newSports[$sport->getId()] = $oldId;

                $listIdSlug[$oldId] = $sport->getSlug();
            }

            foreach ($specialities as $speOld) {
                $spe = new sport;
                $spe->setLevel(2);

                $newId = array_search($speOld['parent'], $newSports);
                $parent = $this->getEm()->getRepository('AppBundle:Sport')->find($newId);
                $spe->setParent($parent);
                $parent->addChild($spe);

                foreach ($speOld['translations'] as $locale => $trans) {
                    $translation = new SportTranslation;
                    $translation->setSport($spe);
                    $translation->setlocale($locale);
                    $translation->setTitle($trans);

                    $spe->addTranslation($translation);
                    if ($locale === 'en') {
                        if ($trans == '') {
                            $this->logger->warning($oldId);
                        }
                        $spe->setName($trans);
                    }
                }
                $this->getEm()->persist($spe);
                $this->getEm()->flush();

                $listIdSlug[$oldId] = $spe->getSlug();
            }

            $this->getEm()->getConnection()->commit();
        } catch (\Exception $e) {
            if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
            }
            $this->logger->error(
                'import.table.sport.insert.query.error',
                array(
                    'sport' => $sports->current(),
                    'message' => $e->getMessage(),
                )
            );
            throw $e;
        }


        return $listIdSlug;
    }

    public function getCats()
    {
        return array(
            42 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Sport d\'hiver',
                    'en' => 'Winter Sports',
                ),
            ),
            43 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Sports de montagne',
                    'en' => 'Mountain Sports',
                ),
            ),
            44 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Sports d\'eau',
                    'en' => 'Water Sports',
                ),
            ),
            45 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Coaching Sportif',
                    'en' => 'Sports Coaching',
                ),
            ),
            47 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Autres',
                    'en' => 'Others',
                ),
            ),
            75 => array(
                'parent' => null,
                'level' => 0,
                'translations' => array(
                    'fr' => 'Cyclisme',
                    'en' => 'Cycling',
                ),
            )
        );
    }

    public function getSports()
    {
        return array(
            48 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Ski',
                    'en' => 'Ski',
                ),
            ),
            49 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Snowboard',
                    'en' => 'Snowboard',
                ),
            ),
            50 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Télémark',
                    'en' => 'Telemark',
                ),
            ),
            51 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Nordique',
                    'en' => 'Nordic ski',
                ),
            ),
            52 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Glisse tractée',
                    'en' => 'Winter Slides Sports',
                ),
            ),
            53 => array(
                'parent' => 42,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Alpinisme',
                    'en' => 'Alpinism',
                ),
            ),
            55 => array(
                'parent' => 43,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Sports de grimpe',
                    'en' => 'Climbing sports',
                ),
            ),
            56 => array(
                'parent' => 43,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Dans les airs',
                    'en' => 'In the air',
                ),
            ),
            57 => array(
                'parent' => 43,
                'level' => 1,
                'translations' => array(
                    'fr' => 'A pied',
                    'en' => 'Walking',
                ),
            ),
            59 => array(
                'parent' => 47,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Autres',
                    'en' => 'Others',
                ),
            ),
            68 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Motorisé - tracté',
                    'en' => 'Motorised and towed Sports',
                ),
            ),
            69 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Bassin',
                    'en' => 'Swimming pool',
                ),
            ),
            72 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Rames/Pagaie',
                    'en' => 'Paddle Sports',
                ),
            ),
            74 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Sub-aquatique',
                    'en' => 'Underwater',
                ),
            ),
            76 => array(
                'parent' => 45,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Course à pied',
                    'en' => 'Running',
                ),
            ),
            77 => array(
                'parent' => 45,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Préparation physique',
                    'en' => 'Physical training',
                ),
            ),
            78 => array(
                'parent' => 45,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Fitness',
                    'en' => 'Fitness',
                ),
            ),
            79 => array(
                'parent' => 45,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Gym',
                    'en' => 'Gym',
                ),
            ),
            80 => array(
                'parent' => 45,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Sport de combat',
                    'en' => 'Fighting Sports',
                ),
            ),
            81 => array(
                'parent' => 47,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Sports de raquette',
                    'en' => 'Racket Sports',
                ),
            ),
            82 => array(
                'parent' => 47,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Golf',
                    'en' => 'Golf',
                ),
            ),
            83 => array(
                'parent' => 47,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Equitation',
                    'en' => 'Horse-riding',
                ),
            ),
            84 => array(
                'parent' => 47,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Danse',
                    'en' => 'Dancing',
                ),
            ),
            87 => array(
                'parent' => 75,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Trial',
                    'en' => 'Trial',
                ),
            ),
            88 => array(
                'parent' => 75,
                'level' => 1,
                'translations' => array(
                    'fr' => 'BMX',
                    'en' => 'BMX',
                ),
            ),
            89 => array(
                'parent' => 75,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Electrique',
                    'en' => 'Electrical',
                ),
            ),
            90 => array(
                'parent' => 75,
                'level' => 1,
                'translations' => array(
                    'fr' => 'FatBike',
                    'en' => 'FatBike',
                ),
            ),
            147 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Vague',
                    'en' => 'Wave',
                ),
            ),
            155 => array(
                'parent' => 44,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Eau-vive',
                    'en' => 'Whitewater',
                ),
            ),
            198 => array(
                'parent' => 75,
                'level' => 1,
                'translations' => array(
                    'fr' => 'VTT',
                    'en' => 'Mountain bike',
                ),
            ),
            209 => array(
                'parent' => 43,
                'level' => 1,
                'translations' => array(
                    'fr' => 'Sport de Vent',
                    'en' => 'Wind sport',
                ),
            )
        );

    }

    public function getSpecs()
    {
        return array(
            91 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Alpin / piste',
                    'en' => 'Alpine / Track',
                ),
            ),
            92 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Hors Piste / FreeRide',
                    'en' => 'Backcountry / FreeRide',
                ),
            ),
            93 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Freestyle',
                    'en' => 'Freestyle',
                ),
            ),
            94 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Skicross',
                    'en' => 'Skicross',
                ),
            ),
            95 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Randonnée',
                    'en' => 'Hiking ski',
                ),
            ),
            96 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Bosses',
                    'en' => 'Moguls',
                ),
            ),
            98 => array(
                'parent' => 50,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Alpin / Piste',
                    'en' => 'Alpine / Track',
                ),
            ),
            99 => array(
                'parent' => 50,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Hors piste / Freeride',
                    'en' => 'Backcountry / FreeRide',
                ),
            ),
            100 => array(
                'parent' => 50,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Freestyle',
                    'en' => 'Freestyle',
                ),
            ),
            101 => array(
                'parent' => 50,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Randonnée',
                    'en' => 'Hiking ski',
                ),
            ),
            102 => array(
                'parent' => 51,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Biathlon',
                    'en' => 'Biathlon',
                ),
            ),
            103 => array(
                'parent' => 51,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Classique',
                    'en' => 'Classical',
                ),
            ),
            104 => array(
                'parent' => 51,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Skating',
                    'en' => 'Skating',
                ),
            ),
            105 => array(
                'parent' => 51,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Raquette',
                    'en' => 'Snowshoes',
                ),
            ),
            106 => array(
                'parent' => 52,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Traineau',
                    'en' => 'Sled',
                ),
            ),
            107 => array(
                'parent' => 52,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Ski-joering',
                    'en' => 'Ski-joering',
                ),
            ),
            108 => array(
                'parent' => 52,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Speed-riding',
                    'en' => 'Speed-riding',
                ),
            ),
            109 => array(
                'parent' => 52,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Kiteski',
                    'en' => 'Kiteski',
                ),
            ),
            110 => array(
                'parent' => 53,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Cascade de glace',
                    'en' => 'Icefall',
                ),
            ),
            111 => array(
                'parent' => 53,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Ski Alpinisme',
                    'en' => 'Alpinism Ski',
                ),
            ),
            112 => array(
                'parent' => 53,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Courses classiques',
                    'en' => 'Classic Races',
                ),
            ),
            113 => array(
                'parent' => 59,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Pilotage sur glace',
                    'en' => 'Ice Driving',
                ),
            ),
            114 => array(
                'parent' => 59,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Monoski',
                    'en' => 'Monoski',
                ),
            ),
            115 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Dry Tooling',
                    'en' => 'Dry Tooling',
                ),
            ),
            116 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Escalade',
                    'en' => 'Climbing',
                ),
            ),
            117 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Slack-line',
                    'en' => 'Slack-line',
                ),
            ),
            118 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Via Ferrata',
                    'en' => 'Via Ferrata',
                ),
            ),
            119 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Via Corda',
                    'en' => 'Via Corda',
                ),
            ),
            120 => array(
                'parent' => 55,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Spéléologie',
                    'en' => 'Speleology',
                ),
            ),
            121 => array(
                'parent' => 56,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Chute Libre',
                    'en' => 'Free Fall',
                ),
            ),
            122 => array(
                'parent' => 56,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Parapente',
                    'en' => 'Paragliding',
                ),
            ),
            123 => array(
                'parent' => 56,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Deltaplane',
                    'en' => 'Hang-glider',
                ),
            ),
            124 => array(
                'parent' => 56,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Wingsuit',
                    'en' => 'Wingsuit',
                ),
            ),
            125 => array(
                'parent' => 57,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Randonnée',
                    'en' => 'Hiking',
                ),
            ),
            126 => array(
                'parent' => 57,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Trail',
                    'en' => 'Trail',
                ),
            ),
            127 => array(
                'parent' => 57,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Marche Nordique',
                    'en' => 'Nordic Walk',
                ),
            ),
            128 => array(
                'parent' => 57,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Course d\'orientation',
                    'en' => 'Orienteering Race',
                ),
            ),
            130 => array(
                'parent' => 57,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Cani-rando',
                    'en' => 'Cani-hike',
                ),
            ),
            131 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Paddle',
                    'en' => 'Paddle',
                ),
            ),
            132 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Aviron',
                    'en' => 'Rowing',
                ),
            ),
            133 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Rafting',
                    'en' => 'Rafting',
                ),
            ),
            134 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Kayak',
                    'en' => 'Kayak',
                ),
            ),
            135 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Canoë',
                    'en' => 'Canoe',
                ),
            ),
            136 => array(
                'parent' => 72,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Hotdog',
                    'en' => 'Hotdog',
                ),
            ),
            143 => array(
                'parent' => 68,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Ski Nautique',
                    'en' => 'Water-ski',
                ),
            ),
            144 => array(
                'parent' => 68,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Wakeboard',
                    'en' => 'Wakeboard',
                ),
            ),
            145 => array(
                'parent' => 68,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Wakeskate',
                    'en' => 'Wakeskate',
                ),
            ),
            146 => array(
                'parent' => 68,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Wakesurf',
                    'en' => 'Wakesurf',
                ),
            ),
            148 => array(
                'parent' => 147,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Surf',
                    'en' => 'Surf',
                ),
            ),
            149 => array(
                'parent' => 147,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Bodyboard',
                    'en' => 'Bodyboard',
                ),
            ),
            150 => array(
                'parent' => 147,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Longboard',
                    'en' => 'Longboard',
                ),
            ),
            151 => array(
                'parent' => 74,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Plongée',
                    'en' => 'Scuba-diving',
                ),
            ),
            152 => array(
                'parent' => 48,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Plongée sous glace',
                    'en' => 'Under ice Scuba-diving',
                ),
            ),
            153 => array(
                'parent' => 74,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Snorkeling',
                    'en' => 'Snorkeling',
                ),
            ),
            154 => array(
                'parent' => 74,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Apnée',
                    'en' => 'Free-diving',
                ),
            ),
            156 => array(
                'parent' => 155,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Canyoning',
                    'en' => 'Canyoning',
                ),
            ),
            157 => array(
                'parent' => 155,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Hydrospeed',
                    'en' => 'Hydrospeed',
                ),
            ),
            158 => array(
                'parent' => 69,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Crawl',
                    'en' => 'Crawl',
                ),
            ),
            159 => array(
                'parent' => 69,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Brasse',
                    'en' => 'Breast Stroke',
                ),
            ),
            160 => array(
                'parent' => 69,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Papillon',
                    'en' => 'Butterfly stroke',
                ),
            ),
            161 => array(
                'parent' => 69,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Dos crawlé',
                    'en' => 'Backstroke',
                ),
            ),
            162 => array(
                'parent' => 69,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Plongeon',
                    'en' => 'Dive',
                ),
            ),
            163 => array(
                'parent' => 76,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Marathon',
                    'en' => 'Marathon',
                ),
            ),
            164 => array(
                'parent' => 76,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Athlétisme',
                    'en' => 'Athletism',
                ),
            ),
            165 => array(
                'parent' => 76,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Raids',
                    'en' => 'Raids',
                ),
            ),
            166 => array(
                'parent' => 76,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Running',
                    'en' => 'Running',
                ),
            ),
            167 => array(
                'parent' => 76,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Ultrafond',
                    'en' => 'Ultramarathon',
                ),
            ),
            168 => array(
                'parent' => 77,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Prise de masse',
                    'en' => 'Weight gain',
                ),
            ),
            169 => array(
                'parent' => 77,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Remise en forme',
                    'en' => 'Fitness programme',
                ),
            ),
            170 => array(
                'parent' => 77,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Perte de poids',
                    'en' => 'Weight Loss',
                ),
            ),
            171 => array(
                'parent' => 77,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Haltérophilie',
                    'en' => 'Weight Lifting',
                ),
            ),
            172 => array(
                'parent' => 77,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Cardio-training',
                    'en' => 'Cardio-training',
                ),
            ),
            173 => array(
                'parent' => 78,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Cardio-boxing',
                    'en' => 'Cardio-boxing',
                ),
            ),
            174 => array(
                'parent' => 78,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Stretching',
                    'en' => 'Stretching',
                ),
            ),
            175 => array(
                'parent' => 79,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Aquagym',
                    'en' => 'Aquagym',
                ),
            ),
            176 => array(
                'parent' => 79,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Aquabike',
                    'en' => 'Aquabike',
                ),
            ),
            177 => array(
                'parent' => 79,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Pilates',
                    'en' => 'Pilates',
                ),
            ),
            178 => array(
                'parent' => 79,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Yoga',
                    'en' => 'Yoga',
                ),
            ),
            179 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Boxe',
                    'en' => 'Boxing',
                ),
            ),
            180 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Judo',
                    'en' => 'Judo',
                ),
            ),
            181 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Karaté',
                    'en' => 'Karate',
                ),
            ),
            182 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Jujitsu',
                    'en' => 'Jujistu',
                ),
            ),
            183 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Krav Maga',
                    'en' => 'Krav Maga',
                ),
            ),
            184 => array(
                'parent' => 80,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Taekwondo',
                    'en' => 'Taekwondo',
                ),
            ),
            185 => array(
                'parent' => 81,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Badminton',
                    'en' => 'Badminton',
                ),
            ),
            186 => array(
                'parent' => 81,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Tennis de table',
                    'en' => 'Ping-pong',
                ),
            ),
            187 => array(
                'parent' => 81,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Squash',
                    'en' => 'Squash',
                ),
            ),
            188 => array(
                'parent' => 81,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Tennis',
                    'en' => 'Tennis',
                ),
            ),
            190 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Randonnée',
                    'en' => 'Horse-riding hike',
                ),
            ),
            191 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Saut d\'obstacles',
                    'en' => 'Jumping',
                ),
            ),
            192 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Passage de galops',
                    'en' => 'Level validation',
                ),
            ),
            193 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Dressage',
                    'en' => 'Dressage',
                ),
            ),
            194 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Poney',
                    'en' => 'Pony',
                ),
            ),
            195 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Voltige',
                    'en' => 'Flit',
                ),
            ),
            196 => array(
                'parent' => 83,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Attelage',
                    'en' => 'Hitch',
                ),
            ),
            199 => array(
                'parent' => 198,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Descente',
                    'en' => 'Downhill',
                ),
            ),
            200 => array(
                'parent' => 198,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Enduro',
                    'en' => 'Enduro',
                ),
            ),
            201 => array(
                'parent' => 155,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Xcountry',
                    'en' => 'Xcountry',
                ),
            ),
            204 => array(
                'parent' => 89,
                'level' => 2,
                'translations' => array(
                    'fr' => 'VTT',
                    'en' => 'Mountain Bike',
                ),
            ),
            205 => array(
                'parent' => 49,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Piste',
                    'en' => 'Track',
                ),
            ),
            206 => array(
                'parent' => 49,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Hors piste/freeride',
                    'en' => 'Freeride',
                ),
            ),
            207 => array(
                'parent' => 49,
                'level' => 2,
                'translations' => array(
                    'fr' => 'Freestyle/Flat',
                    'en' => 'Freestyle/Flat',
                ),
            ),
            208 => array(
                'parent' => 59,
                'level' => 2,
                'translations' => array(
                    'fr' => 'MountainBoard',
                    'en' => 'Mountain Board',
                ),
            )
        );

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
}
