<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\MeetingPoint;
use App\Entity\Session;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price')
            ->add('age')
            ->add('level')
            ->add('nbPersons')
            ->add('duration')
            ->add('customTitle')
            ->add('comment')
            ->add('groupBooking')
            ->add('start')
            ->add('end')
            ->add('pause')
            ->add('sport', EntityType::class, ['class' => Sport::class])
            ->add('speciality', EntityType::class, ['class' => Sport::class])
            ->add('city', EntityType::class, ['class' => City::class])
            ->add('meetingPoint', EntityType::class, ['class' => MeetingPoint::class])
            ->add('customers');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => false,
            'csrf_protection' => false,
            'data_class' => Session::class,
        ]);
    }
}
