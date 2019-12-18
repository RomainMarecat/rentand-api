<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class EmailReminderController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "postEmailReminder"})
     * @Annotations\Post("/email_reminders")
     */
    public function postEmailReminderAction(Request $request)
    {
        return $this->get('manager.email_reminder')->post($request);
    }
}
