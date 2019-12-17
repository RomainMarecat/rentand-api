<?php

namespace App\Controller\Admin;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;

use Doctrine\Common\Collections\ArrayCollection;

class FormController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getFormSport"})
     */
    public function getFormSportAction(Request $request)
    {

        $params = $this->get('app.params');

        $result = [];
        $result['locales'] = $params->getLocales();

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormFamily"})
     */
    public function getFormFamilyAction(Request $request)
    {

        $params = $this->get('app.params');

        $result = [];
        $result['locales'] = $params->getLocales();
        $result['sports'] = $this->getDoctrine()->getRepository('AppBundle:Sport')->findByLevel(0);
        $result['families'] = $this->getDoctrine()->getRepository('AppBundle:Family')->findByParent(null);

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormComment"})
     */
    public function getFormCommentAction(Request $request)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $list = [];
        foreach ($users as $user) {
            if ($user->hasRole('ROLE_PART')) {
                $list[$user->getFirstName()." ".$user->getLastName()] = $user->getId();
            }
        }
        $result['users'] = $list;

        $adverts = $this->getDoctrine()->getRepository('AppBundle:Advert')->findAll();
        $list = [];
        foreach ($adverts as $advert) {
            $list[$advert->getSlug()] = $advert->getId();
        }
        $result['adverts'] = $list;

        return $result;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFormAdvert"})
     */
    public function getFormAdvertAction($type, Request $request)
    {

        $params = $this->get('app.params');

        $result = array('locales' => $params->getLocales());

        $result['users'] = [];
        if ($type == 1) {
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            $list = [];
            foreach ($users as $user) {
                if ($user->hasRole('ROLE_MONO')) {
                    $list[$user->getFirstName(). " " .$user->getLastName()] = $user->getId();
                }
            }
            $result['users'] = $list;
        }
        $structures = $this->getDoctrine()->getRepository('AppBundle:structure')->findAll();
        $list = [];
        foreach ($structures as $structure) {
            $list[$structure->getTitle()] = $structure->getId();
        }
        $result['structures'] = $list;

        $result['levels'] = $params->getLevels();
        $result['ages'] = $params->getAges();
        $result['titles'] = $params->getTitles();
        $result['sports'] = $this->getDoctrine()->getRepository('AppBundle:Sport')->findByLevel(0);

        return $result;
    }
}
