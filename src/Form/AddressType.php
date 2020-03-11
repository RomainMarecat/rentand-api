<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                TextType::class
            )
            ->add(
                'lastname',
                TextType::class
            )
            ->add(
                'firstname',
                TextType::class
            )
            ->add(
                'street',
                TextType::class
            )
            ->add(
                'streetComplement',
                TextType::class,
                [
                    'property_path' => 'street_complement'
                ]
            )
            ->add(
                'zipcode',
                TextType::class
            )
            ->add(
                'city',
                TextType::class
            )
            ->add(
                'country',
                TextType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Address::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
