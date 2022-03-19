<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adresse ?',
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Entrer votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Entrer votre nom'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => '(facultatif) Votre société',
                'required' => false,
                'attr' => [
                    'placeholder' => '(facultatif) Entrer le nom de votre société'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Numéro et libellé de rue',
                'attr' => [
                    'placeholder' => '8 rue des paquerettes...'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Entrer le code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Entrer la ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Choississez votre pays'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Portable',
                'attr' => [
                    'placeholder' => 'Entrer votre numéro de portable'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
