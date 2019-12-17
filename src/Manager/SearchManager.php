<?php

namespace App\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearchManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class SearchManager
{
    protected $em;

    protected $logger;

    protected $params;

    protected $radius;

    protected function calcOffset(array $search)
    {
        $offset = 0;
        if (isset($search['offset'])) {
            $offset = $search['offset'];
        }

        return $offset;
    }

    protected function calcLimit(array $search)
    {
        $limit = 5;
        if (isset($search['limit'])) {
            if ($search['limit'] > 0 and $search['limit'] < 6) {
                $limit = $search['limit'];
            } elseif ($search['limit'] >= 6 and $search['limit'] < 20) {
                return $search['limit'];
            }
        }

        return $limit;
    }

    protected function fetchEntities(array $search)
    {
        $sport = null;
        if (isset($search['sport'])) {
            $sport = $this->getEm()
                ->getRepository('AppBundle:Sport')
                ->findOneById($search['sport']);
        }

        $city = null;
        if (isset($search['city'])) {
            $city = $this->getEm()
                ->getRepository('AppBundle:City')
                ->findOneByGoogleId($search['city']);
            if ($city instanceof City) {
                $search['lat'] = $city->getLat() ?: null;
                $search['lng'] = $city->getLng() ?: null;
                $this->getLogger()->info('city found', array(
                    'city' => $city->getGoogleId(),
                    'lng' => $city->getLng(),
                    'lat' => $city->getLat()
                ));
            } else {
                $this->getLogger()->warning('city not found', array(
                    'city' => $search['city']
                ));
            }
        }

        return array($city, $sport, $search);
    }

    protected function loopToResults(array $search)
    {
        $i = 0;
        do {
            if ($i > 0 and $i < 5) {
                $search['radius'] *= 2;
            }
            if ($i >= 5) {
                $search['radius'] = null;
            }

            if (isset($search['lng']) && isset($search['lat']) && isset($search['radius'])) {
                $search['lat_max'] = $search['lat'] + ($search['radius'] / 100);
                $search['lat_min'] = $search['lat'] - ($search['radius'] / 100);
                $onedeg = abs((40000 / 360) * cos(M_PI * $search['lng'] / 180));
                $calc_lon = $search['radius'] / $onedeg;
                $search['lng_max'] = $search['lng'] + $calc_lon;
                $search['lng_min'] = $search['lng'] - $calc_lon;

                $this->getLogger()->info('Create radius with data', array(
                    'city' => $search['city'],
                    'lng' => $search['lng'],
                    'lat' => $search['lat'],
                ));
            }

            $adverts =
                new ArrayCollection(
                    $this->getEm()
                        ->getRepository('AppBundle:Advert')
                        ->findAdvertsBySearchForm($search)
                );
            $this->getLogger()->info(
                'findAdvertsBySearchForm',
                array(
                    'adverts is empty' => empty($adverts),
                    'adverts is total results' => count($adverts)
                )
            );

            $i++;
        } while ($adverts instanceof ArrayCollection and $adverts->isEmpty() and $i < 6 and $search['offset'] === 0);

        $this->getLogger()->info('Advert is null ?', array(
            $adverts instanceof ArrayCollection and $adverts->isEmpty()
        ));
        return array($search, $adverts);
    }

    protected function countResults(array $search)
    {
        $count =
            $this->getEm()
                ->getRepository('AppBundle:Advert')
                ->countAdvertBySearchForm($search);

        $count = $count['total'];

        $this->getLogger()->info('Count advert results', array(
            'offset' => $search['offset'], 'count' => $count
        ));

        return $count;
    }

    public function getResultsBySimpleForm(Request $request)
    {
        $titles = $this->getParams()->getTitles();
        $search = $request->request->all();
        $search['radius'] = $this->getRadius() ?: 10;
        $search['limit'] = $this->calcLimit($search);
        $search['offset'] = $this->calcOffset($search);
        list($city, $sport, $search) = $this->fetchEntities($search);
        list($search, $adverts) = $this->loopToResults($search);
        $search['limit'] = null;
        $search['offset'] = null;
        $total = $this->countResults($search);

        return array($sport, $city, $adverts, $titles, $total);
    }

    public function countResultsBySimpleForm(Request $request)
    {
        $search = $request->request->all();
        $search['radius'] = $this->getRadius() ?: 10;
        $search['limit'] = $this->calcLimit($search);
        $search['offset'] = $this->calcOffset($search);
        list($city, $sport, $search) = $this->fetchEntities($search);

        return $this->countResults($search);
    }

    public function getResultsByAdvancedForm(Request $request)
    {
        $titles = $this->getParams()->getTitles();
        $search = $request->request->all();
        $search['radius'] = $this->getRadius() ?: 10;
        $search['limit'] = $this->calcLimit($search);
        $search['offset'] = $this->calcOffset($search);
        list($city, $sport, $search) = $this->fetchEntities($search);
        list($search, $adverts) = $this->loopToResults($search);
        $search['limit'] = null;
        $search['offset'] = null;
        $total = $this->countResults($search);

        return array($sport, $city, $adverts, $titles, $total);
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
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

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

    /**
     * Gets the value of radius.
     *
     * @return mixed
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Sets the value of radius.
     *
     * @param mixed $radius the radius
     *
     * @return self
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }
}
