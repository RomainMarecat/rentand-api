<?php


namespace App\Form;

use Model\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreBookingType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add(
                'language',
                ChoiceType::class,
                array(
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => array_map(
                        function ($language) {
                            return new Language($language);
                        },
                        $options['languages']
                    ),
                    'choices_as_values' => true,
                    'choice_translation_domain' => false,
                )
            )
            ->add(
                'advert',
                HiddenType::class,
                array(
                    'required' => true
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'specialities' => array(),
            'cities' => array(),
            'levels' => array(),
            'languages' => array(),
            'ages' => array(),
            'specialities' => array(),
            'data_class' => 'Model\PreBooking',
            'allow_extra_fields' => true,
            'csrf_protection' => true
        ));
    }
}
