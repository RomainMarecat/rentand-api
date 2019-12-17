<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class AdvertSportController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSportById"})
     * @Annotations\Get("/adverts_sports/{advertSport}")
     */
    public function getAdvertSportByIdAction($advertSport)
    {
        return $this->get('manager.advert_sport')->getAdvertSportById($advertSport);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportLevels"})
     * @Annotations\Get("/adverts/{advert}/sports/{sport}/advert_sport")
     */
    public function getAdvertSportAction($advert, $sport)
    {
        $advertSport = $this->get('manager.advert_sport')->getAdvertSport($advert, $sport);

        return $advertSport;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportLevels"})
     * @Annotations\Get("/advert_sports/{advertSport}/levels")
     */
    public function getSportLevelsAction($advertSport)
    {
        $levels = $this->get('manager.advert_sport')
            ->getLevelsBySport($advertSport);

        return $levels;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportAges"})
     * @Annotations\Get("/advert_sports/{advertSport}/ages")
     */
    public function getSportAgesAction($advertSport)
    {
        $ages = $this->get('manager.advert_sport')
            ->getAgesBySport($advertSport);

        return $ages;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSportSpecialities"})
     * @Annotations\Get("/advert_sports/{advertSport}/specialities")
     */
    public function getAdvertSportsSpecialitiesAction(Request $request, $advertSport)
    {
        $advertSports = $this->get('manager.advert_sport')
            ->getSpecialitiesBy($advertSport);

        return $advertSports;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSportSports"})
     * @Annotations\Get("/advert_sports/sports")
     */
    public function getAdvertSportsSportsAction(Request $request)
    {
        $advertSport = $request->query->get('advert_sports');
        $advertSports = $this->get('manager.advert_sport')
            ->getSportsBy($advertSport);

        return $advertSports;
    }
}
