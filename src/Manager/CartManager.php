<?php

namespace App\Manager;

use App\Entity\Cart;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateCart(Cart $cart): Cart
    {
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $item->setPrice($item->getSession()->getPrice());
            if ($item->getProduct()->getPrice() > 0) {
                $item->setPrice($item->getProduct()->getPrice());
            }
            $locale = $cart->getUser()->getUserMetadata()->getMotherLang() instanceof Language ?
                $cart->getUser()->getUserMetadata()->getMotherLang()->getISO6392() : 'fr';

            $translatedName = $item->getProduct()->getTranslations()[$locale] ?? $item->getProduct()->getName();

            $item->setName($translatedName);
            $item->setCode($item->getProduct()->getId());
            $total += $item->getPrice() * $item->getQuantity();
        }
        $cart->setTotal($total);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }
}
