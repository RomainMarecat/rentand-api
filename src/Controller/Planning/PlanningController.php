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
        $user = $this->getDoctrine()->getRepository('App:User')->findOneByPlanningId($id);

        if (!is_object($user) || ($locale != 'fr' && $locale != 'en')) {
            return false;
        }

        $titles = $this->get('app.params')->getTitles();

        $dataUser = [];


        $users = $user->getAdverts()->map(function ($item) {
            if ($item->getEnabled()) {
                return $item;
            }
        });

        foreach ($users as $user) {
            if (!is_null($user)) {
                $dataUser[$user->getId()] = array(
                    'title' => $titles[$user->getTitle()][$locale],
                    'sports' => array(),
                    'cities' => array()
                );
                $userId = $user->getId();


                if (!is_null($user->getSports())) {
                    foreach ($user->getSports() as $sport) {
                        $sportId = $sport->getId();
                        $dataUser[$userId]['sports'][$sportId] = array('title' => $sport->getSport()->getTranslations()[$locale]->getTitle());

                        if (!is_null($sport->getSpecialities())) {
                            foreach ($sport->getSpecialities() as $speciality) {
                                $specialityId = $speciality->getId();
                                $dataUser[$userId]['sports'][$sportId]['specialities'][$specialityId] = $speciality->getTranslations()[$locale]->getTitle();
                            }
                        }
                    }
                }

                if (!is_null($user->getCities())) {
                    foreach ($user->getCities() as $city) {
                        $cityId = $city->getGoogleId();

                        $dataUser[$userId]['cities'][$cityId] = array('title' => $city->getTitle());
                        foreach ($city->getMeetings() as $meeting) {
                            if ($meeting->getAdvert() == $user) {
                                $meetingId = $meeting->getId();
                                $dataUser[$userId]['cities'][$cityId]['meetings'][$meetingId] = $meeting->getTitle();
                            }
                        }
                    }
                }
            }
        }
        return $dataUser;
    }
}
