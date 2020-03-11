<?php

namespace App\Form;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', NumberType::class)
            ->add(
                'product',
                EntityType::class,
                [
                    'class' => Product::class,
                ]
            )
            ->add(
                'session',
                EntityType::class,
                [
                    'class' => Session::class
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => CartItem::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            ]
        );
    }
}
