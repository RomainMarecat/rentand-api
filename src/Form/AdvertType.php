<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('birthdate', BirthdayType::class, array(
                'input' => 'string',
                'widget' => 'choice',
                'placeholder' => ''))
            ->add('gender', ChoiceType::class, array(
                'choices' => array('Homme' => true, 'Femme' => false),
                'choices_as_values' => true))
            ->add('languages', LanguageType::class, array(
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'choices_as_values' => true))
            ->add(
                $builder->create('city', FormType::class, array(
                    'required' => false,
                    'label' => false,
                    'attr' => array('style' => 'display:none;')))
                    ->add('name', HiddenType::class, array('attr' => array('class' => 'advert_city')))
                    ->add('place_id', HiddenType::class, array('attr' => array('class' => 'advert_city')))
                    ->add(
                        $builder->create('geometry', FormType::class, array('label' => false))
                            ->add(
                                $builder->create('location', FormType::class, array('label' => false))
                                    ->add('lng', HiddenType::class, array('attr' => array('class' => 'advert_city')))
                                    ->add('lat', HiddenType::class, array('attr' => array('class' => 'advert_city')))
                            )
                            ->add(
                                $builder->create('viewport', FormType::class, array('label' => false))
                                    ->add('south', HiddenType::class, array('attr' => array('class' => 'advert_city advert_city_viewport')))
                                    ->add('west', HiddenType::class, array('attr' => array('class' => 'advert_city advert_city_viewport')))
                                    ->add('north', HiddenType::class, array('attr' => array('class' => 'advert_city advert_city_viewport')))
                                    ->add('east', HiddenType::class, array('attr' => array('class' => 'advert_city advert_city_viewport')))
                            )
                    )
            )
            ->add('perimeter', IntegerType::class, array(
                'data' => 5,
                'attr' => array('min' => 0)))
            ->add('sports', CollectionType::class, array(
                'entry_type' => SportType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'entry_options' => array('label' => false),
                'required' => false
            ))
            ->add('translations', FormType::class, array('label' => false))

            // ->add('diploma_title', TextType::class, array(
            //     'required' => false))

            // ->add('diploma_advert', FileType::class, array(
            //     'data_class' => null,
            //     'required' => false))

            ->add('image', HiddenType::class)
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary')
            ));


        foreach ($options['locales'] as $locale) {
            $translations = $builder->get('translations');
            $translations->add($locale, FormType::class, array(
                'label' => false,
                'required' => false));

            $translation = $translations->get($locale);
            $translation
                ->add('title', TextType::class, array(
                    'attr' => array('class' => 'advert_translations')))
                ->add('description1', TextareaType::class, array('required' => false))
                ->add('description2', TextareaType::class, array('required' => false))
                ->add('description3', TextareaType::class, array('required' => false));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array(),
            'structures' => array(),
        ));
    }
}
