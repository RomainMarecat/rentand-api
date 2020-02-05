<?php

namespace App\Controller\Front;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSearch"})
     * @Annotations\Get("/search/{criteria}")
     * @param $criteria
     * @return array|ArrayCollection
     */
    public function getSearchAction($criteria)
    {
        $results = array();
        $criteria = json_decode($criteria, true);
        if (is_array($criteria) and
            (key_exists('sport', $criteria) or key_exists('city', $criteria))
        ) {
            $results = new ArrayCollection(
                $this->getDoctrine()
                    ->getRepository('App:Advert')
                    ->findAdvertsByCriteria($criteria)
            );
        }

        return $results;
    }

    /**
     * @Annotations\View(serializerGroups={"getSimpleSearch"})
     * @Annotations\Get("/simple/search")
     */
    public function getSimpleSearchAction()
    {
        $params = $this->get('app.params');
        $titles = $params->getTitles();
        $sports =
            new ArrayCollection(
                $this->getDoctrine()
                    ->getRepository('App:Sport')
                    ->findSportsByLevel(0)
            );

        return array(
            'sports' => $sports,
            'titles' => $titles
        );
    }

    /**
     * @Annotations\View(serializerGroups={"postSimpleSearch"})
     * @Annotations\Post("/simple/search")
     * @param Request $request
     * @return array
     */
    public function postSimpleSearchAction(Request $request)
    {
        list($sport, $city, $users, $titles, $total) = $this->get('manager.search')
            ->getResultsBySimpleForm($request);
        return array(
            'sport' => $sport,
            'city' => $city,
            'adverts' => $users,
            'titles' => $titles,
            'total' => $total
        );
    }


    /**
     * @Annotations\View(serializerGroups={"countPostSimpleSearch"})
     * @Annotations\Post("/count/simple/search")
     * @param Request $request
     * @return
     */
    public function countPostSimpleSearchAction(Request $request)
    {
        $countNextResults = $this->get('manager.search')
            ->countResultsBySimpleForm($request);

        return $countNextResults;
    }

    /**
     * @Annotations\View(serializerGroups={"getAdvancedSearch"})
     * @Annotations\Get("/advanced/search")
     */
    public function getAdvancedSearchAction()
    {
        $params = $this->get('app.params');
        $levels = $params->getLevels();
        $ages = $params->getAges();
        $titles = $params->getTitles();
        $sports =
            new ArrayCollection(
                $this->getDoctrine()
                    ->getRepository('App:Sport')
                    ->findSportsByLevel(0)
            );

        return array(
            'levels' => $levels,
            'ages' => $ages,
            'sports' => $sports,
            'titles' => $titles
        );
    }

    /**
     * @Annotations\View(serializerGroups={"postAdvancedSearch"})
     * @Annotations\Post("/advanced/search")
     * @param Request $request
     * @return array
     */
    public function postAdvancedSearchAction(Request $request)
    {
        list($sport, $city, $users, $titles, $total) = $this->get('manager.search')
            ->getResultsByAdvancedForm($request);
        return array(
            'sport' => $sport,
            'city' => $city,
            'adverts' => $users,
            'titles' => $titles,
            'total' => $total
        );
    }

    /**
     * @Annotations\View(serializerGroups={"getFormSearch"})
     */
    public function getFormSearchAction()
    {

        $params = $this->get('app.params');

        $levels = $params->getLevels();
        $ages = $params->getAges();
        $titles = $params->getTitles();
        $sports = $this->getDoctrine()
            ->getRepository('App:Sport')
            ->findSportsByLevel(1);

        return array(
            'levels' => $levels,
            'ages' => $ages,
            'sports' => $sports,
            'titles' => $titles
        );
    }
}
