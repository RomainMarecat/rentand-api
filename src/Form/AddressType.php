<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
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
                'street',
                TextType::class,
                array(
                    'required' => false,
                    'translation_domain' => false
                )
            )
            ->add(
                'postalCode',
                TextType::class,
                array(
                    'required' => false,
                    'translation_domain' => false
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'required' => false,
                    'translation_domain' => false
                )
            )
            ->add(
                'country',
                CountryType::class,
                array(
                    'translation_domain' => false
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\Address',
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
