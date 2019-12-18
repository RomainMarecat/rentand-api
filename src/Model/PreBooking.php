<?php

namespace App\Model;

/**
 * PreBooking Model Class
 */
class PreBooking
{
    protected $ages;

    protected $levels;

    protected $paramsLevels;

    protected $paramsAges;

    protected $languages;

    protected $cities;

    protected $city;

    protected $sport;

    protected $sports;

    protected $specialities;

    protected $user;

    protected $meetings;

    protected $coach;

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
     * Sets the value of ages.
     *
     * @param mixed $ages the ages
     *
     * @return self
     */
    public function setAges($ages)
    {
        $this->ages = $ages;

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
     * Sets the value of levels.
     *
     * @param mixed $levels the levels
     *
     * @return self
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
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
     * Sets the value of languages.
     *
     * @param mixed $languages the languages
     *
     * @return self
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

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
     * Sets the value of cities.
     *
     * @param mixed $cities the cities
     *
     * @return self
     */
    public function setCities($cities)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Gets the value of specialities.
     *
     * @return mixed
     */
    public function getSpecialities()
    {
        return $this->specialities;
    }

    /**
     * Sets the value of specialities.
     *
     * @param mixed $specialities the specialities
     *
     * @return self
     */
    public function setSpecialities($specialities)
    {
        $this->specialities = $specialities;

        return $this;
    }

    /**
     * Gets the value of advert.
     *
     * @return mixed
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Sets the value of advert.
     *
     * @param mixed $user the advert
     *
     * @return self
     */
    public function setAdvert($user)
    {
        $this->advert = $user;

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
     * Sets the value of sports.
     *
     * @param mixed $sports the sports
     *
     * @return self
     */
    public function setSports($sports)
    {
        $this->sports = $sports;

        return $this;
    }

    /**
     * Gets the value of paramsLevels.
     *
     * @return mixed
     */
    public function getParamsLevels()
    {
        return $this->paramsLevels;
    }

    /**
     * Sets the value of paramsLevels.
     *
     * @param mixed $paramsLevels the params levels
     *
     * @return self
     */
    public function setParamsLevels($paramsLevels)
    {
        $this->paramsLevels = $paramsLevels;

        return $this;
    }

    /**
     * Gets the value of paramsAges.
     *
     * @return mixed
     */
    public function getParamsAges()
    {
        return $this->paramsAges;
    }

    /**
     * Sets the value of paramsAges.
     *
     * @param mixed $paramsAges the params ages
     *
     * @return self
     */
    public function setParamsAges($paramsAges)
    {
        $this->paramsAges = $paramsAges;

        return $this;
    }

    /**
     * Gets the value of meetings.
     *
     * @return mixed
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * Sets the value of meetings.
     *
     * @param mixed $meetings the meetings
     *
     * @return self
     */
    public function setMeetings($meetings)
    {
        $this->meetings = $meetings;

        return $this;
    }

    /**
     * Gets the value of city.
     *
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the value of city.
     *
     * @param mixed $city the city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param mixed $user the user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of sport.
     *
     * @return mixed
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Sets the value of sport.
     *
     * @param mixed $sport the sport
     *
     * @return self
     */
    public function setSport($sport)
    {
        $this->sport = $sport;

        return $this;
    }
}
