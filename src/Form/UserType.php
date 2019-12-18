<?php

namespace App\Form;

use App\Form\AddressType;
use App\Form\PhoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'lastName',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'type',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'accessToken',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            ->add(
                'nationality',
                TextType::class,
                array(
                    'translation_domain' => false
                )
            )
            // ->add(
            //     'birthdate',
            //     BirthdayType::class,
            //     array(
            //         'label' => false,
            //         'input'  => 'datetime',
            //         'widget' => 'choice',
            //         'translation_domain' => false,
            //         'data' => new \DateTime('01-01-1980'),
            //         'years' => range(1940, 2016),
            //     )
            // )

            ->add(
                'gender',
                CheckboxType::class,
                array(
                    'translation_domain' => false
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\User',
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
