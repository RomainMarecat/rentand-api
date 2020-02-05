<?php

namespace App\Controller\Restricted;

use App\Manager\BookingManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class BookingsController extends AbstractFOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"booking"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return object|null
     */
    public function getBookingAction($booking, BookingManager $bookingManager)
    {
        return $bookingManager->get($booking);
    }

    /**
     * @Annotations\View(serializerGroups={"booking"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param BookingManager $bookingManager
     *
     * @return \App\Entity\Booking
     */
    public function postBookingAction(Request $request, BookingManager $bookingManager)
    {
        return $bookingManager->post($request);
    }

    /**
     * @Annotations\View(serializerGroups={"booking"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return object|null
     */
    public function patchBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->patch($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"booking"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return object|null
     */
    public function putBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->put($request, $booking);
    }

    /**
     * @Annotations\View(serializerGroups={"booking"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param                $booking
     * @param BookingManager $bookingManager
     *
     * @return object|null
     */
    public function deleteBookingAction(Request $request, $booking, BookingManager $bookingManager)
    {
        return $bookingManager->delete($request, $booking);
    }
}
