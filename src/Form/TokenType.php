<?php

namespace App\Form;

use App\Entity\Token;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object')
            ->add('clientIp')
            ->add('created')
            ->add('type')
            ->add('used')
            ->add('livemode')
            ->add(
                'card',
                CardType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => Token::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            ]
        );
    }
}
