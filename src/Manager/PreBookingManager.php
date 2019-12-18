<?php

namespace App\Manager;

use Entity\Advert;
use Entity\City;
use Entity\User;
use Helper\ExtendedArrayCollection;
use Model\PreBooking;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PreBookingManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class PreBookingManager
{
    protected $em;

    protected $logger;

    protected $preBooking;

    protected $formFactory;

    protected $params;

    protected function setAdvert(Advert $user)
    {
        $this->preBooking->setLanguages(
            array_combine($user->getLanguages(), $user->getLanguages())
        );

        $this->getLogger()->info(
            'preBooking',
            array(
                'class' => get_class($this->getPreBooking()),
                'languages' => $this->getPreBooking()->getLanguages()
            )
        );

        return $this;
    }

    protected function setUser(User $user)
    {
        $this->preBooking->setUser($user);

        return $this;
    }

    protected function setSports($sports)
    {
        $this->preBooking->setSports(
            $sports->toArray()
        );

        return $this;
    }

    protected function setSport($sport)
    {
        $this->preBooking->setSport($sport);

        return $this;
    }

    protected function setSpecialities($specialities)
    {
        $specs = array();
        $this->logger->info('speciality sql', array($specialities));
        foreach ($specialities as $speciality) {
            if (!isset($specs[$speciality['id']])) {
                $specs[$speciality['id']] = array('translations' => array());
            }
            $specs[$speciality['id']]['translations'][$speciality['locale']] = array(
                'title' => $speciality['title']
            );
            if (isset($speciality['handisport'])) {
                $specs[$speciality['id']]['translations']['handisport'] = array(
                    'title' => 'handisport'
                );
            }
        }
        $this->preBooking->setSpecialities(
            $specs
        );
        $this->logger->info('speciality sql', array($specs));

        return $this;
    }

    protected function setLevels($levels)
    {
        $this->preBooking->setParamsLevels($this->getParams()->getLevels());

        if (isset($levels['levels'])) {
            $this->logger->info('levels', $levels['levels']);
            $this->preBooking->setLevels($levels['levels']);
        }

        return $this;
    }

    protected function setAges($ages)
    {
        $this->preBooking->setParamsAges($this->getParams()->getAges());

        if (isset($ages['ages'])) {
            $this->logger->info('ages', $ages['ages']);
            $this->preBooking->setAges($ages['ages']);
        }

        return $this;
    }

    protected function setMeetings($meetings)
    {
        $result = array();
        foreach ($meetings as $meeting) {
            $this->logger->info('meeting', array($meeting));
            if (isset($meeting['meeting']['id']) and isset($meeting['meeting']['title'])) {
                $result[$meeting['meeting']['title']][$meeting['meeting']['title']] = $meeting['meeting']['id'];
            }
        }
        $this->preBooking->setMeetings(
            $result
        );

        return $this;
    }

    protected function setCities($cities)
    {
        $result = array();
        foreach ($cities as $city) {
            $result[$city['id']] = $city['title'];
        }
        $this->preBooking->setCities(
            $result
        );

        return $this;
    }

    public function setCity($city)
    {
        $this->preBooking->setCity(array());
        if ($city instanceof City) {
            $this->preBooking->setCity(
                array($city->getId())
            );
        }
        return $this;
    }

    public function getPreBookingData(Request $request)
    {
        $this->preBooking = new PreBooking();
        $criteria['advert'] = $request->query->get('advert');
        $criteria['sport'] = $request->query->get('sport');
        $criteria['city'] = $request->query->get('city');
        $this->logger->info('advert query parameter', array($criteria));

        $modelAdvert = $this->getEm()->getRepository('App:Advert')
            ->findPreBookingBy($criteria);

        $modelAdvertSports = new ExtendedArrayCollection(
            $this->getEm()->getRepository('App:Sport')
                ->findPreBookingBy(array('advert' => $criteria['advert']))
        );

        if (!isset($criteria['sport']) and !$modelAdvertSports->isEmpty()) {
            $criteria['sport'] = $modelAdvertSports->first()->getId();
        }
        if (isset($criteria['sport']) and !$modelAdvertSports->isEmpty()) {
            $sports = $modelAdvertSports->map(function ($item) {
                return $item->getId();
            })->toArray();

            if (in_array($criteria['sport'], $sports)) {
                $this->setSport($criteria['sport']);
            }
        }

        if (isset($criteria['sport'])) {
            $modelAdvertSportsSpecialities = new ExtendedArrayCollection(
                $this->getEm()->getRepository('App:Sport')
                    ->findPreBookingSpecialitiesBy($criteria)
            );
            $this->setSpecialities($modelAdvertSportsSpecialities);

            $modelLevels = $this->getEm()->getRepository('App:AdvertSport')
                ->findPreBookingLevelsBy($criteria);

            $this->setLevels($modelLevels);
            $this->setAges($modelLevels);

            $this->logger->info('levels', array($this->preBooking->getLevels()));
        }


        $modelCities = $this->getEm()->getRepository('App:City')
            ->findPreBookingCitiesBy($criteria);
        $this->logger->debug('modelCities', array('cities' => $modelCities));
        if (isset($criteria['city'])) {
            $modelCity = $this->getEm()->getRepository('App:City')
                ->findOneById($criteria['city']);

            if (!$modelCity) {
                $modelCity = $this->getEm()->getRepository('App:City')
                    ->findOneByTitle(trim($criteria['city']));
            }
            $this->setCity($modelCity);
        }


        $modelMeetings = $this->getEm()->getRepository('App:MeetingPoint')
            ->findPreBookingMeetingsBy(
                array_filter(
                    $criteria,
                    function ($k) {
                        return $k == 'city' or $k == 'advert';
                    },
                    ARRAY_FILTER_USE_KEY
                )
            );
        $firstCity = isset($modelCities[0]) ? $modelCities[0] : null;
        $this->getLogger()->debug('firstCity:', array($firstCity));
        $this->getLogger()->debug('modelMeetings', array('meetings' => $modelMeetings));


        if ($firstCity and isset($firstCity['id'])) {
            foreach ($modelMeetings as $key => $value) {
                $this->getLogger()->debug('value', $value);
            }
            $modelMeetings = array_filter($modelMeetings, function ($meeting) use ($firstCity) {
                return isset($firstCity['id']) and isset($meeting['meeting']['city']['id']) and $firstCity['id'] == $meeting['meeting']['city']['id'];
            });
        }
        $this->logger->debug('modelMeetings', array('meetings' => $modelMeetings));

        if ($modelAdvert instanceof Advert) {
            $this->setAdvert($modelAdvert);
            // $this->setUser($modelAdvert->getUser());
        }

        $this->setSports($modelAdvertSports);
        $this->setCities($modelCities);
        $this->setMeetings($modelMeetings);

        return $this->preBooking;
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
    public function setEm($em)
    {
        $this->em = $em;

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
     * Gets the value of preBooking.
     *
     * @return mixed
     */
    public function getPreBooking()
    {
        return $this->preBooking;
    }

    /**
     * Sets the value of preBooking.
     *
     * @param mixed $preBooking the pre booking
     *
     * @return self
     */
    public function setPreBooking($preBooking)
    {
        $this->preBooking = $preBooking;

        return $this;
    }

    /**
     * Gets the value of formFactory.
     *
     * @return mixed
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Sets the value of formFactory.
     *
     * @param mixed $formFactory the form factory
     *
     * @return self
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;

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
