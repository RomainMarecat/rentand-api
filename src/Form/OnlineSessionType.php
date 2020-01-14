<?php

namespace App\Form;

use App\Entity\OnlineSession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnlineSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sessionType')
            ->add('sessionPrices')
            ->add('sportTeached')
            ->add('cityTeached')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnlineSession::class,
            'csrf_protection' => false
        ]);
    }
}
