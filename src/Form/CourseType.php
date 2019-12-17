<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'start',
                DateTimeType::class,
                array(
                    'required' => false,
                    'widget' => 'single_text'
                )
            )
            ->add(
                'end',
                DateTimeType::class,
                array(
                    'required' => false,
                    'widget' => 'single_text'
                )
            )
            ->add(
                'nbPerson',
                NumberType::class
            )
            ->add(
                'age',
                NumberType::class
            )
            ->add(
                'unitPrice',
                NumberType::class
            )
            ->add(
                'courseType',
                HiddenType::class
            )
            ->add(
                'mongoId',
                HiddenType::class
            )
            ->add(
                'city',
                HiddenType::class
            )
            ->add(
                'sport',
                HiddenType::class
            )
            ->add(
                'speciality',
                HiddenType::class
            )
            ->add(
                'meeting',
                HiddenType::class
            )
            ->add(
                'level',
                HiddenType::class
            )
            ->add(
                'language',
                HiddenType::class
            )
            ->add(
                'lastName',
                HiddenType::class
            )
            ->add(
                'firstName',
                HiddenType::class
            )
            ->add(
                'email',
                HiddenType::class
            )
            ->add(
                'phone',
                HiddenType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\Course',
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
