<?php

namespace App\Controller\Restricted;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class BookingsController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getBooking"})
     * @Security("has_role('ROLE_USER')")
     */
    public function getBookingAction($booking)
    {
        return $this->get('manager.booking')->get($booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBookingAdvert"})
     * @Security("has_role('ROLE_USER')")
     */
    public function getBookingAdvertAction($booking)
    {
        return $this->get('manager.advert')->getAdvertByBooking($booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postBooking"})
     * @Security("has_role('ROLE_USER')")
     */
    public function postBookingAction(Request $request)
    {
        return $this->get('manager.booking')->post($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchBooking"})
     * @Security("has_role('ROLE_USER')")
     */
    public function patchBookingAction(Request $request, $booking)
    {
        return $this->get('manager.booking')->patch($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "putBooking"})
     * @Security("has_role('ROLE_USER')")
     */
    public function putBookingAction(Request $request, $booking)
    {
        return $this->get('manager.booking')->put($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteBooking"})
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteBookingAction(Request $request, $booking)
    {
        return $this->get('manager.booking')->delete($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBookingTranslations"})
     * @Annotations\Get("/booking/translations")
     */
    public function getBookingTranslationsAction()
    {
        $translations = array(
            'ages' => $this->get('app.params')->getAges(),
            'levels' => $this->get('app.params')->getLevels(),
            'languages' => Intl::getLanguageBundle()->getLanguageNames(),
        );

        return $translations;
    }
}
