<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Produit;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

    // Construit le formulaire de création et de modification d'un menu.
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {
        $builder
        // Ajoute le champ du nom du menu.
        ->add('menu_nom',TextType::class,['label' => 'Nom du menu','required' => true,'attr' =>
                    [
                        'class' => 'form-control',
                        'placeholder' => 'Ex : Menu Wacdo',
                    ],
                ]
            )

            // Ajoute le champ de description avec CKEditor.
            ->add('menu_description',CKEditorType::class,
                [
                    'label' => 'Description',
                    'required' => false,
                    'config_name' => 'main_config',
                ]
            )

            // Ajoute le champ du prix du menu.
            ->add('menu_prix',NumberType::class,
            [
                'label' => 'Prix (€)',
                'required' => true,
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' =>
                [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => 'Ex : 12.90',
                ],
                ]
            )

            // Ajoute les produits qui composent le menu.
            ->add('produits',EntityType::class,
                [
                    'class' => Produit::class,
                    // Affiche le nom, la catégorie et la disponibilité de chaque produit.
                    'choice_label' => function (Produit $produit): string {$label =$produit->getProduitNom();

                        if ($produit->getCategorie()!== null)
                        {
                            $label .=' - '. $produit->getCategorie()->getCategorieNom();
                        }
                        if (!$produit->isProduitStock())
                        {
                            $label .=' - Indisponible';
                        }
                        return $label;
                    },

                    // Ajoute les informations utilisées pour filtrer les produits dans le formulaire.
                    'choice_attr' => function (Produit $produit): array
                    {
                        return
                        [
                        'data-product-id' =>(string) $produit->getId(),
                        'data-product-name' =>$produit->getProduitNom(),
                        'data-category' =>$produit->getCategorie()!== null? (string) $produit->getCategorie()->getId(): '',
                        'data-category-name' =>$produit->getCategorie()!== null? $produit->getCategorie()->getCategorieNom(): 'Sans catégorie',
                        'data-stock' =>$produit->isProduitStock()? '1': '0',
                        ];
                    },
                    'label' =>'Produits du menu',
                    'multiple' => true,
                    'expanded' => true,
                    'required' => true,
                    'by_reference' => false,
                ]
            )

            // Ajoute le champ permettant de sélectionner une image.
            ->add('imageFile',FileType::class,
                [
                    'label' => 'Image du menu',
                    'required' => false,
                    'attr' =>
                    [
                        'class' =>'form-control',
                        'accept' =>'image/jpeg,image/png,image/webp',
                    ],
                ]
            )

            // Ajoute le choix permettant d'activer ou désactiver le menu.
            ->add('menu_status',CheckboxType::class,
                [
                    'label' => 'Menu actif',
                    'required' => false,
                    'attr' =>
                    [
                        'class' =>'form-check-input',
                    ],
                ]
            );
    }

    // Associe le formulaire à l'entité Menu.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Menu::class,]);
    }
}