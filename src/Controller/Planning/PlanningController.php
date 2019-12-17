<?php

namespace App\Controller\Planning;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanningController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getPlanningUserInformations"})
     * @Annotations\Get("/{planning}/user/informations")
     */
    public function getPlanningUserInformationsAction($planning)
    {
        try {
            $informations = $this->get('manager.user')
                ->getUserInformations($planning);
            $codes = Response::HTTP_OK;
        } catch (\Exception $e) {
            $informations = $e->getMessage();
            $codes = Response::HTTP_BAD_REQUEST;
        }

        return $this->view(
            $informations,
            $codes
        );
    }

    public function getUserLocaleAction(Request $request, $id, $locale)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByPlanningId($id);

        if (!is_object($user) || ($locale != 'fr' && $locale != 'en')) {
            return false;
        }

        $titles = $this->get('app.params')->getTitles();

        $dataUser = [];


        $adverts = $user->getAdverts()->map(function ($item) {
            if ($item->getEnabled()) {
                return $item;
            }
        });

        foreach ($adverts as $advert) {
            if (!is_null($advert)) {
                $dataUser[$advert->getId()] = array(
                    'title' => $titles[$advert->getTitle()][$locale],
                    'sports' => array(),
                    'cities' => array()
                );
                $advertId = $advert->getId();


                if (!is_null($advert->getSports())) {
                    foreach ($advert->getSports() as $sport) {
                        $sportId = $sport->getId();
                        $dataUser[$advertId]['sports'][$sportId] = array('title' => $sport->getSport()->getTranslations()[$locale]->getTitle());

                        if (!is_null($sport->getSpecialities())) {
                            foreach ($sport->getSpecialities() as $speciality) {
                                $specialityId = $speciality->getId();
                                $dataUser[$advertId]['sports'][$sportId]['specialities'][$specialityId] = $speciality->getTranslations()[$locale]->getTitle();
                            }
                        }
                    }
                }

                if (!is_null($advert->getCities())) {
                    foreach ($advert->getCities() as $city) {
                        $cityId = $city->getGoogleId();

                        $dataUser[$advertId]['cities'][$cityId] = array('title' => $city->getTitle());
                        foreach ($city->getMeetings() as $meeting) {
                            if ($meeting->getAdvert() == $advert) {
                                $meetingId = $meeting->getId();
                                $dataUser[$advertId]['cities'][$cityId]['meetings'][$meetingId] = $meeting->getTitle();
                            }
                        }
                    }
                }
            }
        }
        return $dataUser;
    }
}
