<?php

namespace App\Controller\Front;

use App\Entity\Family;
use App\Entity\Sport;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class FamilyController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getParentFamilies"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/families/parent")
     */
    public function getParentFamiliesAction()
    {
        return $this->getDoctrine()
            ->getRepository(Family::class)
            ->findBy(['parent' => null]);
    }

    /**
     * @Annotations\View(serializerGroups={"getFamiliesByParent"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/families/parent/{family}")
     * @param $family
     * @return array
     */
    public function getFamiliesByParentAction($family)
    {
        $families = $this->getDoctrine()
            ->getRepository(Family::class)
            ->findSubFamiliesByParent($family);

        $sports = $this->getDoctrine()
            ->getRepository(Sport::class)
            ->findSportsByParentFamily($family);

        return array_merge($sports, $families);
    }
}
