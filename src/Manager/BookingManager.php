<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\DeleteBookingType;
use App\Form\PatchBookingType;
use App\Form\PutBookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BookingManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class BookingManager
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (null === $token = $this->getTokenStorage()->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    public function getByUser()
    {
        return $this->entityManager->getRepository(Booking::class)->findByUser($this->getUser()->getId());
    }

    public function getPaymentsMono()
    {
        return $this->entityManager->getRepository(Booking::class)->findPaymentsByMono($this->getUser()->getId());
    }

    public function get($booking)
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($booking);
        if (!$booking instanceof Booking) {
            throw new HttpException(404, "booking.undefined");
        }

        return $booking;
    }

    public function post(Request $request)
    {
        $user = $this->getUser();
        $booking = new Booking();
        $form = $this->getFormFactory()->create(
            BookingType::class,
            $booking
        );

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() and $form->isValid()) {
                $booking = $form->getData();
                try {
                    $this->entityManager->getConnection()->beginTransaction();
                    foreach ($booking->getCourses() as $course) {
                        $course->setBooking($booking);
                        $course->setCreatedAt(new \DateTime());
                        $course->setUpdatedAt(new \DateTime());

                        $this->entityManager->persist($course);
                    }

                    $booking->setUser($user);
                    $booking->setStatut(0);
                    $booking->setCreatedAt(new \DateTime());
                    $booking->setUpdatedAt(new \DateTime());
                    $user = $this->entityManager
                        ->getRepository('App:Advert')
                        ->findOneById($booking->getAdvert());
                    $booking->setAdvert($user);
                    if ($user instanceof Advert) {
                        $booking->setCancellation($user->getCancel());
                    }

                    $this->entityManager->persist($booking);
                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();
                } catch (\Exception $e) {
                    if ($this->entityManager->getConnection()->getTransactionNestingLevel() != 0) {
                        $this->entityManager->getConnection()->rollBack();
                    }
                    $this->getLogger()->error(
                        'booking error persist',
                        array(
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'l.' => $e->getLine()
                        )
                    );
                    throw new HttpException(400, "Error persist booking");
                }
            }
        }
        $this->getLogger()->debug(
            'booking in request',
            array(
                'method' => $request->getMethod(),
                'isSubmitted' => $form->isSubmitted(),
                'isValid' => $form->isValid(),
                'errors' => $this->getFormParser()->parseErrors($form),
                'request' => $request->request->all(),
            )
        );

        return $booking;
    }

    public function patch(Request $request, $booking)
    {
        $user = $this->getUser();
        $booking = $this->entityManager
            ->getRepository(Booking::class)->find($booking);
        if (!is_object($booking)) {
            throw new HttpException(404, "Booking is undefined");
        }
        $form = $this->getFormFactory()->create(
            PatchBookingType::class,
            $booking
        );

        if ($request->isMethod('PATCH')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() and $form->isValid()) {
                try {
                    $booking = $form->getData();

                    $this->entityManager->merge($booking);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    throw new HttpException(400, "Error merge booking");
                }
            }
        }
        $this->getLogger()->debug(
            'booking in request',
            array(
                'method' => $request->getMethod(),
                'isSubmitted' => $form->isSubmitted(),
                'isValid' => $form->isValid(),
                'form_name' => $form->getName(),
                'mango_pay_transaction_id' => $booking->getMangoPayTransactionId(),
                'code' => $booking->getCode(),
                'statut' => $booking->getStatut(),
                'price' => $booking->getPrice(),
                'wallet_id' => $booking->getWalletId(),
                'errors' => $this->getFormParser()->parseErrors($form),
                'request' => $request->request->all(),
            )
        );

        return $booking;
    }


    public function put(Request $request, $booking)
    {
        $user = $this->getUser();
        $booking = $this->entityManager
            ->getRepository(Booking::class)->find($booking);
        if (!is_object($booking)) {
            throw new HttpException(404, "Booking is undefined");
        }
        $form = $this->getFormFactory()->create(
            PutBookingType::class,
            $booking
        );

        if ($request->isMethod('PUT')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() and $form->isValid()) {
                try {
                    $booking = $form->getData();
                    $booking->setCode(RandomHelper::getRandomString());
                    $booking->setStatut(1);


                    $this->entityManager->merge($booking);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    throw new HttpException(400, "Error merge booking");
                }
            }
        }
        $this->getLogger()->debug(
            'booking in request',
            array(
                'method' => $request->getMethod(),
                'isSubmitted' => $form->isSubmitted(),
                'isValid' => $form->isValid(),
                'form_name' => $form->getName(),
                'mango_pay_transaction_id' => $booking->getMangoPayTransactionId(),
                'code' => $booking->getCode(),
                'statut' => $booking->getStatut(),
                'price' => $booking->getPrice(),
                'wallet_id' => $booking->getWalletId(),
                'errors' => $this->getFormParser()->parseErrors($form),
                'request' => $request->request->all(),
            )
        );

        return $booking;
    }

    public function delete(Request $request, $booking)
    {
        $user = $this->getUser();
        $booking = $this->entityManager
            ->getRepository(Booking::class)->find($booking);
        if (!is_object($booking)) {
            throw new HttpException(404, "Booking is undefined");
        }
        $form = $this->getFormFactory()->create(
            DeleteBookingType::class,
            $booking
        );

        if ($request->isMethod('DELETE')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() and $form->isValid()) {
                try {
                    $booking = $form->getData();
                    $booking->setStatut("-1");

                    $this->entityManager->merge($booking);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    throw new HttpException(400, "Error merge booking");
                }
            }
        }
        $this->getLogger()->debug(
            'booking in request',
            array(
                'method' => $request->getMethod(),
                'isSubmitted' => $form->isSubmitted(),
                'isValid' => $form->isValid(),
                'errors' => $this->getFormParser()->parseErrors($form),
                'request' => $request->request->all(),
                // 'booking' => $this->getSerializer()->serialize($booking, 'json')
            )
        );

        return $booking;
    }
}
