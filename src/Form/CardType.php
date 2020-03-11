<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressCity')
            ->add('addressCountry')
            ->add('addressLine1')
            ->add('addressLine1Check')
            ->add('addressLine2')
            ->add('addressState')
            ->add('addressZip')
            ->add('addressZipCheck')
            ->add('brand')
            ->add('country')
            ->add('cvcCheck')
            ->add('dynamicLast4')
            ->add('expMonth')
            ->add('expYear')
            ->add('funding')
            ->add('last4')
//            ->add('metadata')
            ->add('name')
            ->add('tokenizationMethod')
            ->add('payment');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ]
        );
    }
}
