<?php

namespace App\Controller\Front;

use App\Entity\MeetingPoint;
use App\Manager\MeetingManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;

class MeetingPointController extends AbstractFOSRestController
{
    /**
     * find a Meeting point
     *
     * @Annotations\View(serializerGroups={"meetingPoint"}, serializerEnableMaxDepthChecks=true)
     * @param $meeting
     * @param MeetingManager $meetingManager
     * @return MeetingPoint
     */
    public function getMeetingAction($meeting, MeetingManager $meetingManager)
    {
        return $meetingManager->get($meeting);
    }
}
