<?php

namespace App\Controller\Front;

use App\Entity\Phone;
use App\Manager\PhoneManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class PhoneController extends AbstractFOSRestController
{
    /**
     * Find phone information by number id
     *
     * @Annotations\View(serializerGroups={"phone"}, serializerEnableMaxDepthChecks=true)
     * @param $phone
     * @param PhoneManager $phoneManager
     * @return Phone
     */
    public function getPhoneAction($phone, PhoneManager $phoneManager)
    {
        return $phoneManager->get($phone);
    }
}
