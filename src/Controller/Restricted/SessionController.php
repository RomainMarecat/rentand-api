<?php

namespace App\Controller\Restricted;

use App\Entity\Session;
use App\Form\SessionType;
use App\Manager\SessionManager;
use App\Traits\FormErrorFormatter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class SessionController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"addSession"})
     * @Annotations\Post("/sessions")
     * @param SessionManager $sessionManager
     * @param Request $request
     * @return Session|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addSessionAction(SessionManager $sessionManager, Request $request)
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $session->addCustomer($this->getUser());
            return $sessionManager->registerSession($session);
        }
        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }

    /**
     * @Annotations\View(serializerGroups={"removeSession"})
     * @Annotations\Delete("/sessions/{session}")
     * @param SessionManager $sessionManager
     * @param Session $session
     * @return Session|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeSessionAction(SessionManager $sessionManager, Session $session)
    {
        if ($this->getUser()) {
            $sessionManager->removeSession($this->getUser(), $session);
        }

        return null;
    }
}
