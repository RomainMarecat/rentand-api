<?php

namespace App\Controller\Admin;

use App\Services\Params;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class FormController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getFormSport"})
     * @param Request $request
     * @param Params $params
     * @return array
     */
    public function getFormSportAction(Request $request, Params $params)
    {
        $result = [];
        $result['locales'] = $params->getLocales();

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormFamily"})
     * @param Request $request
     * @param Params $params
     * @return array
     */
    public function getFormFamilyAction(Params $params)
    {
        $result = [];
        $result['locales'] = $params->getLocales();
        $result['sports'] = $this->getDoctrine()->getRepository('App:Sport')->findByLevel(0);
        $result['families'] = $this->getDoctrine()->getRepository('App:Family')->findByParent(null);

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormComment"})
     */
    public function getFormCommentAction()
    {

        $users = $this->getDoctrine()->getRepository('App:User')->findAll();
        $list = [];
        foreach ($users as $user) {
            if ($user->hasRole('ROLE_PART')) {
                $list[$user->getFirstName() . " " . $user->getLastName()] = $user->getId();
            }
        }
        $result['users'] = $list;

        $users = $this->getDoctrine()->getRepository('App:Advert')->findAll();
        $list = [];
        foreach ($users as $user) {
            $list[$user->getSlug()] = $user->getId();
        }
        $result['adverts'] = $list;

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormAdvert"})
     * @param $type
     * @param Params $params
     * @return array
     */
    public function getFormAdvertAction($type, Params $params)
    {

        $result = array('locales' => $params->getLocales());

        $result['users'] = [];
        if ($type == 1) {
            $users = $this->getDoctrine()->getRepository('App:User')->findAll();
            $list = [];
            foreach ($users as $user) {
                if ($user->hasRole('ROLE_MONO')) {
                    $list[$user->getFirstName() . " " . $user->getLastName()] = $user->getId();
                }
            }
            $result['users'] = $list;
        }
        $structures = $this->getDoctrine()->getRepository('App:structure')->findAll();
        $list = [];
        foreach ($structures as $structure) {
            $list[$structure->getTitle()] = $structure->getId();
        }
        $result['structures'] = $list;

        $result['levels'] = $params->getLevels();
        $result['ages'] = $params->getAges();
        $result['titles'] = $params->getTitles();
        $result['sports'] = $this->getDoctrine()->getRepository('App:Sport')->findByLevel(0);

        return $result;
    }
}
