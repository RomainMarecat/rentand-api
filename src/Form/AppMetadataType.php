<?php

namespace App\Form;

use App\Entity\AppMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppMetadataType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'admin',
                CheckboxType::class
            )
            ->add(
                'coach',
                CheckboxType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => AppMetadata::class,
                'allow_extra_fields' => true,
                'csrf_protection' => false
            )
        );
    }
}
