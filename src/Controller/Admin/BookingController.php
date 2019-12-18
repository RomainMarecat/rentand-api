<?php

namespace App\Controller\Admin;

use App\Manager\Admin\BookingManager;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends FOSRestController
{
    /**
     * @Annotations\View(serializerGroups={"adminGetBookings"})
     * @Annotations\Get("/bookings")
     * @param ParamFetcherInterface $paramFetcher
     *
     * @param BookingManager $bookingManager
     * @return Response
     *
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing notes.")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="How many notes to return.")
     * @QueryParam(name="order_by", nullable=true, map=true, description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC")
     * @QueryParam(name="filters", nullable=true, map=true, description="Filter by fields. Must be an array ie. &filters[id]=3")
     */
    public function getBookingsAction(ParamFetcherInterface $paramFetcher, BookingManager $bookingManager)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $filters = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

        return $bookingManager->adminCget($filters, $orderBy, $limit, $offset);
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "adminGetBooking"})
     */
    public function getBookingAction($id)
    {
        $booking = $this->getDoctrine()->getRepository('App:Booking')->find($id);

        if (!is_object($booking)) {
            throw $this->createNotFoundException();
        }
        return $booking;
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "adminGetBooking"})
     */
    public function patchBookingAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        $booking = $this->getDoctrine()->getRepository('App:Booking')->find($id);
        if (!is_object($booking)) {
            throw $this->createNotFoundException();
        }

        if (isset($data['status'])) {
            $booking->setStatut($data['status']);
            $em->persist($booking);
            $em->flush();
        }
        return $booking;
    }

}
