<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'walletId',
                TextType::class
            )
            ->add(
                'advert',
                AdvertIdType::class
            )
            ->add(
                'user',
                HiddenType::class
            )
            ->add(
                'price',
                NumberType::class
            )
            ->add(
                'courses',
                CollectionType::class,
                array(
                    'type' => CourseType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => false,
                    'required' => false,
                    'validation_groups' => array('CourseValidationBooking'),
                    'prototype_name' => 'collection'
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\Booking',
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
