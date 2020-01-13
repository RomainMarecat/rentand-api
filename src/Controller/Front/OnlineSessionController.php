<?php

namespace App\Controller\Front;

use App\Entity\OnlineSession;
use App\Form\OnlineSessionType;
use App\Traits\FormErrorFormatter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

class OnlineSessionController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"getOnlineSessions"})
     * @Annotations\Get("/online_sessions")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOnlineSessionsAction(Request $request)
    {
        $onlineSession = new OnlineSession();
        $form = $this->createForm(OnlineSessionType::class, $onlineSession);

        $form->submit($request->query->all(), true);

        if ($form->isSubmitted() && $form->isValid()) {
            return $onlineSessions = $this->getDoctrine()
                ->getRepository(OnlineSession::class)
                ->findByCriteria($onlineSession);
        }

        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }
}
