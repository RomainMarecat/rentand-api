<?php

namespace App\Controller\Restricted;

use App\Entity\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Manager\OrderManager;
use App\Traits\FormErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractFOSRestController
{
    /**
     * Find all orders from user
     * @Annotations\Get("/orders")
     * @Annotations\View(serializerGroups={"orders"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @return Cart
     */
    public function getOrdersAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Order::class)
            ->getOrders($this->getUser());
    }

    /**
     * Find order attach to cart from user
     * @Annotations\Get("/orders/{id}")
     * @Annotations\View(serializerGroups={"order"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @param Order $order
     * @return Cart
     */
    public function getOrderAction(EntityManagerInterface $entityManager, $id)
    {
        return $entityManager->getRepository(Order::class)
            ->getOrder($this->getUser(), $id);
    }

    /**
     * @Annotations\View(serializerGroups={"order"})
     * @Security("has_role('ROLE_USER')")
     * @Annotations\Post("/orders")
     * @param OrderManager $orderManager
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Order|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addOrderAction(
        OrderManager $orderManager,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $order = new Order();
        $data = $request->request->all();

        $form = $this->createForm(OrderType::class, $order);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setUser($this->getUser());
            /** @var Cart $cart */
            $cart = $entityManager->getRepository(Cart::class)
                ->getCurrentCart($this->getUser());
            $order->setCart($cart);
            $order->setDelivery($cart->getDelivery());
            return $orderManager->updateOrder($order);
        }
        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }
}
