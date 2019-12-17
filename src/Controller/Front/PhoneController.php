<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class PhoneController extends FOSRestController
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
