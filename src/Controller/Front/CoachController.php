<?php

namespace App\Controller\Front;

use App\Manager\UserManager;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class CoachController extends AbstractFOSRestController
{
    /**
     * List of all coachs (@param Request $request
     *
     * @param UserManager $userManager
     *
     * @return array
     * @todo need refacto by keywords search)
     *
     * @Annotations\View(serializerGroups={"getUsers"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users")
     */
    public function getCoachsAction(Request $request, UserManager $userManager)
    {
        return $userManager->getUsers();
    }

    /**
     * Find a coach by slug
     *
     * @Annotations\View(serializerGroups={"getUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/users/{slug}")
     * @param             $slug
     * @param UserManager $userManager
     *
     * @return
     */
    public function getCoachByIdAction($slug, UserManager $userManager)
    {
        return $userManager->getUser($slug);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvert"})
     * @param $slug
     *
     * @return array
     */
    public function getAdvertAction($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findPartialOneBySlug($slug);

        $params = array();
        $params['titles'] = $this->get('app.params')->getTitles();
        $params['passions'] = $this->get('app.params')->getPassions();

        return array_merge(
            $user,
            array('params' => $params)
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getUserByAdvert"})
     * @Annotations\Get("/adverts/{advert}/user")
     * @param $user
     *
     * @return mixed
     */
    public function getUserByAdvertAction($user)
    {

        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->findPartialOneByAdvert($user);

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBestAdverts"})
     * @Annotations\Get("/adverts/best/{limit}")
     * @param $limit
     *
     * @return array
     */
    public function getAdvertsBestAction($limit)
    {
        $users = new ArrayCollection($this->getDoctrine()
            ->getRepository('App:Advert')
            ->findByBestAdverts($limit));

        $titles = $this->get('app.params')->getTitles();

        return array(
            'adverts' => $users,
            'titles' => $titles
        );
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertsChampions"})
     * @Annotations\Get("/adverts/champions/{limit}")
     * @param $limit
     *
     * @return array
     */
    public function getAdvertsChampionsAction($limit)
    {
        $users = new ArrayCollection($this->getDoctrine()
            ->getRepository('App:Advert')
            ->findByAdvertsChampions($limit));

        return array('adverts' => $users);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertStars"})
     * @Annotations\Get("/adverts/{advert}/stars")
     * @param $user
     *
     * @return array
     */
    public function getAdvertStarsAction($user)
    {
        $user = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findStarsByAdvert($user);

        return array('advert' => $user);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertLanguages"})
     * @Annotations\Get("/adverts/{advert}/languages")
     * @param $user
     *
     * @return array
     */
    public function getAdvertLanguagesAction($user)
    {
        $user = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findLanguagesByAdvert($user);

        return array('advert' => $user);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertTranslations"})
     * @Annotations\Get("/adverts/{advert}/translations")
     * @param $user
     *
     * @return mixed
     */
    public function getAdvertTranslationsAction($user)
    {
        $user = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findTranslationsByAdvert($user);

        return $user;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertCities"})
     * @Annotations\Get("/adverts/{advert}/cities")
     * @param $user
     *
     * @return array
     */
    public function getAdvertCitiesAction($user)
    {
        $user = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findCitiesByCriteria(array('advert' => $user));

        return array('advert' => $user);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertAdvertSports"})
     * @Annotations\Get("/adverts/{advert}/advert_sports/hydrated")
     * @param $user
     *
     * @return mixed
     */
    public function getAdvertAdvertSportsAction($user)
    {
        $userSports = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findAdvertSportsByAdvert($user);

        return $userSports;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSports"})
     * @Annotations\Get("/adverts/{advert}/advert_sports")
     * @param $user
     *
     * @return array
     */
    public function getAdvertSportsAction($user)
    {
        $userSports = $this->getDoctrine()
            ->getRepository('App:Advert')
            ->findAdvertSportsByCriteria(array('advert' => $user));

        return array('advert_sports' => $userSports);
    }
}
