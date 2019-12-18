<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'countryCode',
                HiddenType::class,
                array(
                    'attr' => array('class' => 'phone_countryCode')
                )
            )
            ->add(
                'countryNumber',
                HiddenType::class,
                array(
                    'attr' => array('class' => 'phone_countryNumber')
                )
            )
            ->add(
                'number',
                TextType::class,
                array(
                    'label' => false,
                    'attr' => array('class' => 'phone_js')
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\Phone',
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
