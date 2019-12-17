<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

class MeetingController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getMeeting"})
     */
    public function getMeetingAction($meeting)
    {
        return $this->get('manager.meeting')->get($meeting);
    }
}
