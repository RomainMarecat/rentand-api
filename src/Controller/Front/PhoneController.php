<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class PhoneController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getPhone"})
     */
    public function getPhoneAction($phone)
    {
        $phone = $this->get('manager.phone')->get($phone);

        return $phone;
    }
}
