<?php

namespace App\Manager;

use App\Entity\Delivery;
use Doctrine\ORM\EntityManagerInterface;

class DeliveryManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function registerDelivery(Delivery $delivery)
    {
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        return $delivery;
    }
}
