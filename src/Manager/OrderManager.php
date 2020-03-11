<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateOrder(Order $order): Order
    {
        $cart = $order->getCart();
        $cart->setOrder($order);
        $cart->setState('confirmation');
        $cart->setStatus('finished');

        $order->setTotal($cart->getTotal());
        $order->setDelivery($cart->getDelivery());
        $order->setUser($cart->getUser());
        $order->setDeliveryFee($cart->getFees());
        $order->setStatus('capture_authorized');

        foreach ($cart->getItems() as $item) {
            $orderItem = new OrderItem();
            $orderItem->setCode($item->getCode());
            $orderItem->setName($item->getName());
            $orderItem->setPrice($item->getPrice());
//            $orderItem->setImage($item->);
            $orderItem->setQuantity($item->getQuantity());
            $orderItem->setIsEticket(false);
            $orderItem->setOrder($order);

            $order->addOrderItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}
