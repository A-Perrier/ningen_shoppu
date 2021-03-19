<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Label;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording', TextType::class, [
                'label' => "Libellé"
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description"
            ])
            ->add('price', MoneyType::class, [
                'label' => "Prix",
                'divisor' => 100
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie",
                'class' => Category::class,
                'choice_label' => "title"
            ])
            ->add('quantityInStock', NumberType::class, [
                'label' => "Quantité en stock",
            ])
            ->add('isOnSale', CheckboxType::class, [
                'label' => "En ligne",
                'required' => false,
                'attr' => [
                    'class' => 'onSale' 
                ]
            ])
            ->add('labels', EntityType::class, [
                'label' => "Labels du produit",
                'class' => Label::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
                
            ])
            ->add('product_images', CollectionType::class, [
                'label' => "Photos du produit",
                'entry_type' => ProductImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
