<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Champ contenant le nom du produit.
            ->add('produit_nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : WacFat',
                ],
            ])

            // Champ contenant la description du produit.
            ->add('produit_description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Description du produit',
                ],
            ])

            // Champ contenant le prix du produit.
            ->add('produit_prix', NumberType::class, [
                'label' => 'Prix (€)',
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0',
                    'placeholder' => 'Ex : 8.50',
                ],
            ])

            // Case indiquant si le produit est disponible.
            ->add('produit_stock', CheckboxType::class, [
                'label' => 'Produit disponible en stock',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])

            // Champ temporaire contenant le nom de l'image.
            ->add('produit_image', TextType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : wacfat.jpg',
                ],
            ])

            // Liste des catégories disponibles.
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categorie_nom',
                'label' => 'Catégorie',
                'placeholder' => 'Aucune catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Lie le formulaire à l'entité Produit.
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}