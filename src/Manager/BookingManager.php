<?php

namespace App\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Advert;
use Entity\Booking;
use Form\BookingType;
use Form\DeleteBookingType;
use Form\PatchBookingType;
use Form\PutBookingType;
use Helper\RegexHelper;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Zeemono\VoucherBundle\Helper\RandomHelper;
use Zeemono\VoucherBundle\Services\FormParser;

/**
 * Class BookingManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class BookingManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    protected $formFactory;

    protected $tokenStorage;

    protected $serializer;

    protected $formParser;

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
        return $this->getEm()->getRepository('AppBundle:Booking')->findByUser($this->getUser()->getId());
    }

    public function getPaymentsMono()
    {
        return $this->getEm()->getRepository('AppBundle:Booking')->findPaymentsByMono($this->getUser()->getId());
    }

    public function get($booking)
    {
        $booking = $this->getEm()->getRepository('AppBundle:Booking')->find($booking);
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
                    $this->getEm()->getConnection()->beginTransaction();
                    foreach ($booking->getCourses() as $course) {
                        $course->setBooking($booking);
                        $course->setCreatedAt(new \DateTime());
                        $course->setUpdatedAt(new \DateTime());

                        $this->getEm()->persist($course);
                    }

                    $booking->setUser($user);
                    $booking->setStatut(0);
                    $booking->setCreatedAt(new \DateTime());
                    $booking->setUpdatedAt(new \DateTime());
                    $advert = $this->getEm()
                        ->getRepository('AppBundle:Advert')
                        ->findOneById($booking->getAdvert());
                    $booking->setAdvert($advert);
                    if ($advert instanceof Advert) {
                        $booking->setCancellation($advert->getCancel());
                    }

                    $this->getEm()->persist($booking);
                    $this->getEm()->flush();
                    $this->getEm()->getConnection()->commit();
                } catch (\Exception $e) {
                    if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                        $this->getEm()->getConnection()->rollBack();
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
        $booking = $this->getEm()
            ->getRepository('AppBundle:Booking')->find($booking);
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

                    $this->getEm()->merge($booking);
                    $this->getEm()->flush();
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
        $booking = $this->getEm()
            ->getRepository('AppBundle:Booking')->find($booking);
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


                    $this->getEm()->merge($booking);
                    $this->getEm()->flush();
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
        $booking = $this->getEm()
            ->getRepository('AppBundle:Booking')->find($booking);
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

                    $this->getEm()->merge($booking);
                    $this->getEm()->flush();
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

    public function registerBookings($users, $adverts)
    {

        $bookings = new \ArrayIterator($this->getConnection()->fetchAll('
            SELECT
                * ,
                u.email as Uemail,
                a.email as Aemail,
            FROM booking b
            LEFT JOIN user u ON u.id = b.user_id
            LEFT JOIN advert a ON a.id = b.advert_id
            WHERE statut is not null'));
        $total = $bookings->count();
        $this->logger->info(
            'import.table.booking.array.bookings',
            array(
                'total' => $total
            )
        );

        $this->getEm()->getConnection()->beginTransaction();

        foreach ($bookings as $bookingV1) {
            try {
                if (isset($bookingV1['user_id'])) {
                    $user = $this->getEm()->getRepository('AppBundle:User')->findOneByEmail($bookingV1['Uemail']);
                }
                if (isset($adverts[$bookingV1['advert_id']])) {
                    $advert = $this->getEm()->getRepository('AppBundle:Advert')->findOneByEmail($newAdvertId['Aemail']);
                }

                if (isset($advert) && isset($user)) {
                    $booking = new Booking;

                    $booking->setAdvert($advert);
                    $booking->setUser($user);

                    $booking->setStatut($bookingV1['statut']);
                    $booking->setStartDate(new \DateTime($bookingV1['date']));
                    $booking->setEndDate(new \DateTime($bookingV1['date']));
                    $booking->setPrice($bookingV1['price'] + $bookingV1['fees']);
                    $booking->setTransaction($bookingV1['discount_id']);
                    $booking->setCreatedAt(new \DateTime($bookingV1['created_at']));
                    $booking->setUpdatedAt(new \DateTime($bookingV1['updated_at']));

                    $this->getEm()->persist($booking);
                    $this->getEm()->flush();
                }
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.booking.insert.query.error',
                    array(
                        'booking' => $bookingV1,
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    )
                );
                throw $e;
                // interface with user to manage entity
            }
        }

        $this->getEm()->getConnection()->commit();

        return $this;
    }

    /**
     * Gets the value of em.
     *
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Sets the value of em.
     *
     * @param mixed $em the em
     *
     * @return self
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * Gets the value of connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the value of connection.
     *
     * @param mixed $connection the connection
     *
     * @return self
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Gets the value of logger.
     *
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the value of logger.
     *
     * @param mixed $logger the logger
     *
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Gets the value of regexHelper.
     *
     * @return mixed
     */
    public function getRegexHelper()
    {
        return $this->regexHelper;
    }

    /**
     * Sets the value of regexHelper.
     *
     * @param mixed $regexHelper the regex helper
     *
     * @return self
     */
    public function setRegexHelper(RegexHelper $regexHelper)
    {
        $this->regexHelper = $regexHelper;

        return $this;
    }

    /**
     * Gets the value of formFactory.
     *
     * @return mixed
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Sets the value of formFactory.
     *
     * @param mixed $formFactory the form factory
     *
     * @return self
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;

        return $this;
    }

    /**
     * Gets the value of tokenStorage.
     *
     * @return mixed
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * Sets the value of tokenStorage.
     *
     * @param mixed $tokenStorage the token storage
     *
     * @return self
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * Gets the value of serializer.
     *
     * @return mixed
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Sets the value of serializer.
     *
     * @param mixed $serializer the serializer
     *
     * @return self
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Gets the value of formParser.
     *
     * @return mixed
     */
    public function getFormParser()
    {
        return $this->formParser;
    }

    /**
     * Sets the value of formParser.
     *
     * @param mixed $formParser the form parser
     *
     * @return self
     */
    public function setFormParser(FormParser $formParser)
    {
        $this->formParser = $formParser;

        return $this;
    }
}
