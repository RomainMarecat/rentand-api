<?php

namespace App\Manager;

use Entity\AdvertSport;
use Entity\City;
use Entity\Meeting;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EmailReminderManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class EmailReminderManager
{
    protected $em;

    protected $logger;

    protected $meetingManager;

    protected $advertManager;

    protected $cityManager;

    protected $advertSportManager;

    protected $sportManager;

    public function post(Request $request)
    {
        $course = $request->request->get('course');
        $this->getLogger()->info("debug course", array($course));
        try {
            $this->getLogger()->info("debug course", array($course));
            if (isset($course['sport'])) {
                $advertSport = $this->getAdvertSportManager()->getAdvertSportById($course['sport']);
                $this->getLogger()->debug("debug advertSport", array('instanceof advertSport' => $advertSport instanceof AdvertSport));
                if ($advertSport instanceof AdvertSport and method_exists($advertSport, 'getSport')) {
                    $course['sport'] = $this->getSportManager()->get($advertSport->getSport());
                }
            }
            if (isset($course['profile'])) {
                $course['advert'] = $this->getAdvertManager()->getAdvert($course['profile']);
            }
            if (isset($course['city'])) {
                $city = $this->getCityManager()->getCityByGoogleId($course['city']);
                if ($city instanceof City and method_exists($city, 'getTitle')) {
                    $course['city_google_id'] = $course['city'];
                    $course['city'] = $city->getTitle();
                }
            }
            if (isset($course['speciality'])) {
                $course['speciality'] = $this->getSportManager()->get($course['speciality']);
            }

            if (isset($course['meeting'])) {
                if ($course['meeting'] == "booking.title.meeting.home") {
                    $meeting = new Meeting();
                    $meeting->setTitle($this->getTranslator()->trans("booking.title.meeting.home"));
                } else {
                    $meeting = $this->getMeetingManager()->get($course['meeting']);
                }

                if ($meeting instanceof Meeting) {
                    if (method_exists($meeting, 'getTitle')) {
                        $course['meeting_id'] = $course['meeting'];
                        $course['meeting'] = $meeting->getTitle();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->getLogger()->error(
                "post course reminder error",
                array(
                    'message' => $e->getMessage(),
                    'l.' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                )
            );
        }

        return $course;
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
     * Gets the value of meetingManager.
     *
     * @return mixed
     */
    public function getMeetingManager()
    {
        return $this->meetingManager;
    }

    /**
     * Sets the value of meetingManager.
     *
     * @param mixed $meetingManager the meeting manager
     *
     * @return self
     */
    public function setMeetingManager($meetingManager)
    {
        $this->meetingManager = $meetingManager;

        return $this;
    }

    /**
     * Gets the value of advertManager.
     *
     * @return mixed
     */
    public function getAdvertManager()
    {
        return $this->advertManager;
    }

    /**
     * Sets the value of advertManager.
     *
     * @param mixed $advertManager the advert manager
     *
     * @return self
     */
    public function setAdvertManager($advertManager)
    {
        $this->advertManager = $advertManager;

        return $this;
    }

    /**
     * Gets the value of cityManager.
     *
     * @return mixed
     */
    public function getCityManager()
    {
        return $this->cityManager;
    }

    /**
     * Sets the value of cityManager.
     *
     * @param mixed $cityManager the city manager
     *
     * @return self
     */
    public function setCityManager($cityManager)
    {
        $this->cityManager = $cityManager;

        return $this;
    }

    /**
     * Gets the value of advertSportManager.
     *
     * @return mixed
     */
    public function getAdvertSportManager()
    {
        return $this->advertSportManager;
    }

    /**
     * Sets the value of advertSportManager.
     *
     * @param mixed $advertSportManager the advert sport manager
     *
     * @return self
     */
    public function setAdvertSportManager($advertSportManager)
    {
        $this->advertSportManager = $advertSportManager;

        return $this;
    }

    /**
     * Gets the value of sportManager.
     *
     * @return mixed
     */
    public function getSportManager()
    {
        return $this->sportManager;
    }

    /**
     * Sets the value of sportManager.
     *
     * @param mixed $sportManager the sport manager
     *
     * @return self
     */
    public function setSportManager($sportManager)
    {
        $this->sportManager = $sportManager;

        return $this;
    }
}
