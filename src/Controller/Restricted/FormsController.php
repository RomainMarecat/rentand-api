<?php

namespace App\Controller\Restricted;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class FormsController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getFormAdvert"})
     */
    public function getFormAdvertAction(Request $request)
    {

        $params = $this->get('app.params');

        $result = array(
            'locales' => $params->getLocales(),
            'levels' => $params->getLevels(),
            'ages' => $params->getAges(),
            'titles' => $params->getTitles(),
            'passions' => $params->getPassions(),
            'sports' => $this->getDoctrine()->getRepository('App:Sport')->findByLevel(0)
        );

        return $result;
    }
}
