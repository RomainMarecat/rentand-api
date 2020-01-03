<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Language;
use App\Entity\UserMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserMetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['constraints' => new NotBlank()])
            ->add('lastname', TextType::class, ['constraints' => new NotBlank()])
            ->add('gender', TextType::class, ['constraints' => new NotBlank()])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => new NotBlank()])
            ->add(
                'nationality',
                EntityType::class,
                array(
                    'class' => Country::class,
                    'translation_domain' => false,
                    'constraints' => new NotBlank()
                )
            )
            ->add(
                'motherLang',
                EntityType::class,
                [
                    'class' => Language::class,
                    'property_path' => 'mother_lang',
                    'constraints' => new NotBlank()
                ]
            )
            ->add('address', AddressType::class, ['constraints' => new NotBlank()])
            ->add('phone', PhoneType::class, ['constraints' => new NotBlank()]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserMetadata::class,
            'allow_extra_fields' => false,
            'csrf_protection' => false
        ]);
    }
}
