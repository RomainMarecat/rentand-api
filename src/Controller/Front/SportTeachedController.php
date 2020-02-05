<?php

namespace App\Controller\Front;

use App\Manager\SportTeachedManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class SportTeachedController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSportTeachedById"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports_teached/{sportTeached}")
     * @param                     $sportTeached
     * @param SportTeachedManager $sportTeachedManager
     *
     * @return mixed
     */
    public function getAdvertSportByIdAction($sportTeached, SportTeachedManager $sportTeachedManager)
    {
        return $sportTeachedManager->getAdvertSportById($sportTeached);
    }

    /**
     * @Annotations\View(serializerGroups={"getSportsTeachedByUser"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports_teached/sports/user_id/{user}")
     * @param                     $user
     * @param SportTeachedManager $sportTeachedManager
     *
     * @return
     */
    public function getSportsTeachedByUserAction($user, SportTeachedManager $sportTeachedManager)
    {
        return $sportTeachedManager->getSportsTeachedByUser($user);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportLevels"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/advert_sports/{sportTeached}/levels")
     * @param $sportTeached
     * @param SportTeachedManager $sportTeachedManager
     * @return
     */
    public function getSportLevelsAction($sportTeached, SportTeachedManager $sportTeachedManager)
    {
        $levels = $sportTeachedManager
            ->getLevelsBySport($sportTeached);

        return $levels;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getSportAges"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/advert_sports/{sportTeached}/ages")
     * @param $sportTeached
     * @param SportTeachedManager $sportTeachedManager
     * @return
     */
    public function getSportAgesAction($sportTeached, SportTeachedManager $sportTeachedManager)
    {
        $ages = $sportTeachedManager
            ->getAgesBySport($sportTeached);

        return $ages;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSportSpecialities"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/advert_sports/{sportTeached}/specialities")
     * @param Request $request
     * @param $sportTeached
     * @param SportTeachedManager $sportTeachedManager
     * @return
     */
    public function getAdvertSportsSpecialitiesAction(Request $request, $sportTeached, SportTeachedManager $sportTeachedManager)
    {
        return $sportTeachedManager
            ->getSpecialitiesBy($sportTeached);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getAdvertSportSports"})
     * @Annotations\Get("/advert_sports/sports")
     * @param Request $request
     * @param SportTeachedManager $sportTeachedManager
     * @return
     */
    public function getAdvertSportsSportsAction(Request $request, SportTeachedManager $sportTeachedManager)
    {
        $sportTeached = $request->query->get('advert_sports');
        $sportTeacheds = $sportTeachedManager
            ->getSportsBy($sportTeached);

        return $sportTeacheds;
    }
}
