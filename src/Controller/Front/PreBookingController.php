<?php

namespace App\Controller\Front;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class PreBookingController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"newPreBookings"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/new")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
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
     * @Annotations\View(serializerGroups={"preBookingTranslations"}, serializerEnableMaxDepthChecks=true)
     * @Annotations\Get("/translations")
     * @param Request $request
     * @return array
     */
    public function getPreBookingsTranslationsAction(Request $request)
    {
        return [
            'ages' => $this->get('app.params')->getAges(),
            'levels' => $this->get('app.params')->getLevels(),
            'languages' => Intl::getLanguageBundle()->getLanguageNames(),
        ];
    }
}
