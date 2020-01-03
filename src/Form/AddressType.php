<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'street',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'zipcode',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'country',
                EntityType::class,
                array(
                    'class' => Country::class,
                    'translation_domain' => false
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Address::class,
                'allow_extra_fields' => false,
                'csrf_protection' => false
            )
        );
    }
}
