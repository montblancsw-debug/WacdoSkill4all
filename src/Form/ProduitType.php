<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ obligatoire contenant le nom du produit.
            ->add('produit_nom', TextType::class, [
                'label' => 'Nom du produit',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : WacFat',
                    'minlength' => 2,
                    'maxlength' => 255,
                ],
            ])

            // Utilise CKEditor pour la description facultative.
            ->add('produit_description', CKEditorType::class, [
                'label' => 'Description',
                'required' => false,
                'config_name' => 'main_config',
            ])

            // Champ obligatoire contenant le prix.
            ->add('produit_prix', NumberType::class, [
                'label' => 'Prix (€)',
                'required' => true,
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => 'Ex : 8.50',
                ],
            ])

            // Indique si le produit est disponible.
            ->add('produit_stock', CheckboxType::class, [
                'label' => 'Produit disponible en stock',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])

            // Champ facultatif contenant le nom de l'image.
            ->add('produit_image', TextType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : wacfat.jpg',
                    'maxlength' => 255,
                ],
            ])

            // Permet de sélectionner une catégorie.
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