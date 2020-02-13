<?php

namespace App\Controller\Front;

use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class SportController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getSports"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports")
     * @param Request $request
     * @return
     */
    public function getSportsAction(Request $request)
    {
        return $this->getDoctrine()
            ->getRepository(Sport::class)
            ->findAllOrderedByName(
                (int) $request->query->get('level', 0),
                $request->query->get('sport', null)
            );
    }

    /**
     * @Annotations\View(serializerGroups={"getSports"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/sports/{slug}")
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return
     */
    public function getSportAction($slug, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Sport::class)
            ->findOneBySlug($slug);
    }
}
