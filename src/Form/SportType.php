<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class SportType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id', HiddenType::class)
            ->add('order', HiddenType::class)
            ->add('levels', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__levels__',
                'label' => false,
                'entry_options' => array('label' => false),
                'required' => false
            ))
            ->add('specialities', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__specialities__',
                'label' => false,
                'entry_options' => array('label' => false),
                'required' => false
            ))
            ->add('pictures', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__pictures__',
                'label' => false,
                'entry_options' => array('label' => false),
                'required' => false
            ))
            ->add('translations', CollectionType::class, array(
                'entry_type' => SportTranslationType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__translations__',
                'label' => false,
                'entry_options' => array('label' => false),
                'required' => false
            ));

        // ->add('handi', HiddenType::class)
        // ->add('description', HiddenType::class);
        // ->add('specialities', HiddenType::class)
        // ->add('levels', HiddenType::class)
        // ->add('pictures', HiddenType::class);
    }
}
