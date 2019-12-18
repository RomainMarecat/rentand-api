<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class FamilyController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getParentFamilies"})
     * @Annotations\Get("/families/parent")
     */
    public function getParentFamiliesAction()
    {
        $families = $this->getDoctrine()
            ->getRepository('App:Family')
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
            ->getRepository('App:Family')
            ->findSubFamiliesByParent($family);

        $sports = $this->getDoctrine()
            ->getRepository('App:Sport')
            ->findSportsByParentFamily($family);

        return array_merge($sports, $families);
    }
}
