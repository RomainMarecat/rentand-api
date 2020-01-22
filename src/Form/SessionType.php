<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\MeetingPoint;
use App\Entity\OnlineSession;
use App\Entity\Session;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('age')
            ->add('level')
            ->add('nbPersons')
            ->add('duration')
            ->add('customTitle')
            ->add('comment')
            ->add('groupBooking')
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => new NotBlank()])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => new NotBlank()])
            ->add('pause')
            ->add('sport', EntityType::class, ['class' => Sport::class])
            ->add('onlineSession', EntityType::class, ['class' => OnlineSession::class, 'property_path' => 'online_session'])
            ->add('speciality', EntityType::class, ['class' => Sport::class])
            ->add('city', EntityType::class, ['class' => City::class])
            ->add(
                'meetingPoint',
                EntityType::class,
                ['class' => MeetingPoint::class, 'property_path' => 'meeting_point']
            )
            ->add('customers');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'data_class' => Session::class,
        ]);
    }
}
