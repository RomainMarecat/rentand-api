<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class PreBookingController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "newPreBookings"})
     * @Annotations\Get("/new")
     */
    public function newPreBookingsAction(Request $request)
    {
        $preBooking = $this->get('manager.prebooking')
            ->getPreBookingData($request);

        return $this->view(array(
            'preBooking' => $preBooking,
        ), Response::HTTP_OK);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "preBookingTranslations"})
     * @Annotations\Get("/translations")
     */
    public function getPreBookingsTranslationsAction(Request $request)
    {
        $translations = array(
            'ages' => $this->get('app.params')->getAges(),
            'levels' => $this->get('app.params')->getLevels(),
            'languages' => Intl::getLanguageBundle()->getLanguageNames(),
        );


        return $translations;
    }
}
