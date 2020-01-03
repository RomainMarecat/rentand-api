<?php

namespace App\Controller\Restricted;

use App\Manager\BookingManager;
use App\Manager\UserManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class BookingsController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"Default", "getBooking"})
     * @Security("has_role('ROLE_USER')")
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return
     */
    public function getBookingAction($booking, BookingManager $bookingManager)
    {
        return $bookingManager->get($booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "getBookingAdvert"})
     * @Security("has_role('ROLE_USER')")
     * @param             $booking
     * @param UserManager $userManager
     *
     * @return mixed
     */
    public function getBookingAdvertAction($booking, UserManager $userManager)
    {
        return $userManager->getAdvertByBooking($booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "postBooking"})
     * @Security("has_role('ROLE_USER')")
     * @param Request        $request
     * @param BookingManager $bookingManager
     *
     * @return
     */
    public function postBookingAction(Request $request, BookingManager $bookingManager)
    {
        return $bookingManager->post($request);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "patchBooking"})
     * @Security("has_role('ROLE_USER')")
     * @param Request        $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return
     */
    public function patchBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->patch($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "putBooking"})
     * @Security("has_role('ROLE_USER')")
     * @param Request        $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return
     */
    public function putBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->put($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "deleteBooking"})
     * @Security("has_role('ROLE_USER')")
     * @param Request        $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return
     */
    public function deleteBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->delete($request, $booking);
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
