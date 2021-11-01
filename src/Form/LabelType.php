<?php

namespace App\Form;

use App\Entity\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LabelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du label'
            ])
            ->add('description', TextType::class, [
                'label' => "Description du label"
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => "Image du label",
                'label_attr' => [
                    'class' => "img-label"
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Label::class,
        ]);
    }
}
