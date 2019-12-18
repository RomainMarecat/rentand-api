<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class MeetingController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getMeeting"})
     */
    public function getMeetingAction($meeting)
    {
        return $this->get('manager.meeting')->get($meeting);
    }
}
