<?php

namespace App\Form;

use App\Entity\ContactEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                "label" => "Adresse email"
            ])
            ->add('subject', TextType::class, [
                "label" => "Sujet de votre message"
            ])
            ->add('content', TextareaType::class, [
                "label" => "Votre message"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactEmail::class,
        ]);
    }
}
