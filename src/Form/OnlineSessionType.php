<?php

namespace App\Form;

use App\Entity\CityTeached;
use App\Entity\OnlineSession;
use App\Entity\SportTeached;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnlineSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'sportTeached',
                EntityType::class,
                [
                    'class' => SportTeached::class,
                    'property_path' => 'sport_teached'
                ]
            )
            ->add(
                'cityTeached',
                EntityType::class,
                [
                    'class' => CityTeached::class,
                    'property_path' => 'city_teached'
                ]
            )
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnlineSession::class,
            'csrf_protection' => false
        ]);
    }
}
