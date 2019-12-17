<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class FamilyController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getParentFamilies"})
     * @Annotations\Get("/families/parent")
     */
    public function getParentFamiliesAction()
    {
        $families = $this->getDoctrine()
            ->getRepository('AppBundle:Family')
            ->findByParent(null);

        return $families;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getFamiliesByParent"})
     * @Annotations\Get("/families/parent/{family}")
     */
    public function getFamiliesByParentAction($family)
    {
        $families = $this->getDoctrine()
            ->getRepository('AppBundle:Family')
            ->findSubFamiliesByParent($family);

        $sports = $this->getDoctrine()
            ->getRepository('AppBundle:Sport')
            ->findSportsByParentFamily($family);

        return array_merge($sports, $families);
    }
}
