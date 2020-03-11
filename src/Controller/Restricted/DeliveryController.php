<?php

namespace App\Controller\Restricted;

use App\Entity\Cart;
use App\Entity\Delivery;
use App\Form\DeliveryType;
use App\Manager\CartManager;
use App\Manager\DeliveryManager;
use App\Traits\FormErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class DeliveryController extends AbstractFOSRestController
{
    /**
     * Find all deliveries from user
     * @Annotations\Get("/deliveries")
     * @Annotations\View(serializerGroups={"deliveries"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @return Cart
     */
    public function getDeliveriesAction(EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Delivery::class)
            ->getDeliveries($this->getUser());
    }

    /**
     * Find delivery attach to cart from user
     * @Annotations\Get("/deliveries/cart/{cart}")
     * @Annotations\View(serializerGroups={"deliveries"}, serializerEnableMaxDepthChecks=true)
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $entityManager
     * @param Cart $cart
     * @return Cart
     */
    public function getDeliveryFromCartAction(EntityManagerInterface $entityManager, Cart $cart)
    {
        return $entityManager->getRepository(Delivery::class)
            ->getDelivery($this->getUser(), $cart);
    }

    /**
     * @Annotations\View(serializerGroups={"delivery"})
     * @Security("has_role('ROLE_USER')")
     * @Annotations\Post("/deliveries")
     * @param DeliveryManager $deliveryManager
     * @param CartManager $cartManager
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Delivery|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addDeliveryAction(
        DeliveryManager $deliveryManager,
        CartManager $cartManager,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $delivery = new Delivery();
        $data = $request->request->all();
        if (isset($data['billing']['address_complement'])) {
            $data['billing']['addressComplement'] = $data['billing']['address_complement'];
            unset($data['billing']['address_complement']);
        }

        $form = $this->createForm(DeliveryType::class, $delivery);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $delivery->setUser($this->getUser());
            $cart = $entityManager->getRepository(Cart::class)
                ->getCurrentCart($this->getUser());
            $delivery->addCart($cart);
            $cartManager->updateCart($cart);
            return $deliveryManager->registerDelivery($delivery);
        }
        return FormErrorFormatter::getErrorsAsJsonResponse($form);
    }
}
