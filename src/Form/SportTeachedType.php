<?php

namespace App\Form;

use App\Entity\SportTeached;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportTeachedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderNumber')
            ->add('levels')
            ->add('ages')
            ->add('translations')
            ->add('user')
            ->add('sport')
            ->add('specialities')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SportTeached::class,
        ]);
    }
}
