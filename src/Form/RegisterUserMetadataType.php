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

class RegisterUserMetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['constraints' => new NotBlank()])
            ->add('lastname', TextType::class, ['constraints' => new NotBlank()]);
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
