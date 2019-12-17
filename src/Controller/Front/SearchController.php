<?php

namespace App\Controller\Front;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getSearch"})
     * @Annotations\Get("/search/{criteria}")
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
                    ->getRepository('AppBundle:Advert')
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
                    ->getRepository('AppBundle:Sport')
                    ->findSportsByLevel(0)
            );

        return array(
            'sports' => $sports,
            'titles' => $titles
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postSimpleSearch"})
     * @Annotations\Post("/simple/search")
     */
    public function postSimpleSearchAction(Request $request)
    {
        list($sport, $city, $adverts, $titles, $total) = $this->get('manager.search')
            ->getResultsBySimpleForm($request);
        return array(
            'sport' => $sport,
            'city' => $city,
            'adverts' => $adverts,
            'titles' => $titles,
            'total' => $total
        );
    }


    /**
     * @Annotations\View(serializerGroups={"Default", "countPostSimpleSearch"})
     * @Annotations\Post("/count/simple/search")
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
                    ->getRepository('AppBundle:Sport')
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
     * @Annotations\View(serializerGroups={"Default", "postAdvancedSearch"})
     * @Annotations\Post("/advanced/search")
     */
    public function postAdvancedSearchAction(Request $request)
    {
        list($sport, $city, $adverts, $titles, $total) = $this->get('manager.search')
            ->getResultsByAdvancedForm($request);
        return array(
            'sport' => $sport,
            'city' => $city,
            'adverts' => $adverts,
            'titles' => $titles,
            'total' => $total
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormSearch"})
     */
    public function getFormSearchAction()
    {

        $params = $this->get('app.params');

        $levels = $params->getLevels();
        $ages = $params->getAges();
        $titles = $params->getTitles();
        $sports = $this->getDoctrine()
            ->getRepository('AppBundle:Sport')
            ->findSportsByLevel(1);

        return array(
            'levels' => $levels,
            'ages' => $ages,
            'sports' => $sports,
            'titles' => $titles
        );
    }
}
