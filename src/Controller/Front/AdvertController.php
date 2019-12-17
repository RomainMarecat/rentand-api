<?php

namespace App\Controller\Front;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;

class AdvertController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getAdverts"})
     */
    public function getAdvertsAction(Request $request)
    {
        $sport = $request->query->get('sport');
        $lng = $request->query->get('lng');
        $lat = $request->query->get('lat');
        $level = $request->query->get('level');
        $age = $request->query->get('age');
        $language = $request->query->get('language');
        // $people = $request->query->get('people');

        $location = array(
            'lat' => $lat,
            'lng' => $lng);

        $adverts = [];
        $radius = 10;
        while (empty($adverts)) {
            $adverts = $this->searchAdverts($sport, $location, $level, $age, $language, $radius);
            $radius = $radius + 10;
        }

        return array($radius => $adverts);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertById"})
     * @Annotations\Get("/adverts/{advert}/id")
     */
    public function getAdvertByIdAction($advert)
    {
        return $this->get("manager.advert")
            ->getAdvert($advert);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvert"})
     */
    public function getAdvertAction($slug)
    {
        $advert = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findPartialOneBySlug($slug);
        // ->findBySlug($slug);

        $params = array();
        $params['titles'] = $this->get('app.params')->getTitles();
        $params['passions'] = $this->get('app.params')->getPassions();

        return array_merge(
            $advert,
            array('params' => $params)
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByAdvert"})
     * @Annotations\Get("/adverts/{advert}/user")
     */
    public function getUserByAdvertAction($advert)
    {

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findPartialOneByAdvert($advert);

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBestAdverts"})
     * @Annotations\Get("/adverts/best/{limit}")
     */
    public function getAdvertsBestAction($limit)
    {
        $adverts = new ArrayCollection($this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findByBestAdverts($limit));

        $titles = $this->get('app.params')->getTitles();

        return array(
            'adverts' => $adverts,
            'titles' => $titles
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertsChampions"})
     * @Annotations\Get("/adverts/champions/{limit}")
     */
    public function getAdvertsChampionsAction($limit)
    {
        $adverts = new ArrayCollection($this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findByAdvertsChampions($limit));

        return array('adverts' => $adverts);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertStars"})
     * @Annotations\Get("/adverts/{advert}/stars")
     */
    public function getAdvertStarsAction($advert)
    {
        $advert = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findStarsByAdvert($advert);

        return array('advert' => $advert);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertLanguages"})
     * @Annotations\Get("/adverts/{advert}/languages")
     */
    public function getAdvertLanguagesAction($advert)
    {
        $advert = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findLanguagesByAdvert($advert);

        return array('advert' => $advert);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertTranslations"})
     * @Annotations\Get("/adverts/{advert}/translations")
     */
    public function getAdvertTranslationsAction($advert)
    {
        $advert = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findTranslationsByAdvert($advert);

        return $advert;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertCities"})
     * @Annotations\Get("/adverts/{advert}/cities")
     */
    public function getAdvertCitiesAction($advert)
    {
        $advert = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findCitiesByCriteria(array('advert' => $advert));

        return array('advert' => $advert);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertAdvertSports"})
     * @Annotations\Get("/adverts/{advert}/advert_sports/hydrated")
     */
    public function getAdvertAdvertSportsAction($advert)
    {
        $advertSports = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findAdvertSportsByAdvert($advert);

        return $advertSports;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSports"})
     * @Annotations\Get("/adverts/{advert}/advert_sports")
     */
    public function getAdvertSportsAction($advert)
    {
        $advertSports = $this->getDoctrine()
            ->getRepository('AppBundle:Advert')
            ->findAdvertSportsByCriteria(array('advert' => $advert));

        return array('advert_sports' => $advertSports);
    }
}
