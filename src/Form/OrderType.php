<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Delivery;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'payment',
                PaymentType::class
            )
            ->add(
                'cart',
                EntityType::class,
                [
                    'class' => Cart::class
                ]
            )
            ->add(
                'delivery',
                EntityType::class,
                [
                    'class' => Delivery::class
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => Order::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            ]
        );
    }
}
