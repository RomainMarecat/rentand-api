<?php

namespace App\Controller\Front;

use App\Entity\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class ParameterController extends AbstractFOSRestController
{
    /**
     * @View(serializerGroups={"getParameter"}, serializerEnableMaxDepthChecks=true)
     * @Get("/parameters")
     * @param EntityManagerInterface $entityManager
     *
     * @return array
     */
    public function getParametersAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Parameter::class)->findAll();
    }
}
