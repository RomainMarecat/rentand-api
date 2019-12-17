<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;

class EmailReminderController extends FOSRestController
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
