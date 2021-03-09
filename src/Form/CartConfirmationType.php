<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => "Nom complet"
            ])
            ->add('address', TextType::class, [
                'label' => "Adresse"
            ])
            ->add('postalCode', NumberType::class, [
                'label' => "Code postal"
            ])
            ->add('city', TextType::class, [
                'label' => "Ville"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
