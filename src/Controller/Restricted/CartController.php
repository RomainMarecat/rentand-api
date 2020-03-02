<?php

namespace App\Controller\Restricted;

use App\Entity\Cart;
use App\Form\CartType;
use App\Manager\CartManager;
use App\Traits\FormErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractFOSRestController
{
    /**
     * Find cart
     * @Annotations\Get("/cart")
     * @Annotations\View(serializerGroups={"cart"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @return Cart
     */
    public function currentCartAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Cart::class)
            ->getCurrentCart($this->getUser());
    }

    /**
     * Update Cart
     *
     * @Annotations\Put("/cart")
     * @Annotations\View(serializerGroups={"cart"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @param CartManager $cartManager
     * @param Request $request
     * @return Cart|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCartAction(EntityManagerInterface $entityManager, CartManager $cartManager, Request $request)
    {
        $cart = $entityManager->getRepository(Cart::class)
            ->getCurrentCart($this->getUser());
        $form = $this->createForm(CartType::class, $cart);

        $form->submit($request->request->all(), false);

        if ($form->isSubmitted() && $form->isValid()) {
            return $cartManager->updateCart($cart);
        }

        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }
}
