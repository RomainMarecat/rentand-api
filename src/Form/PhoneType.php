<?php

namespace App\Form;

use App\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                )
            )
            ->add(
                'countryNumber',
                HiddenType::class,
                array(
                )
            )
            ->add(
                'number',
                TextType::class,
                array(
                    'constraints' => new NotBlank(),
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Phone::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
