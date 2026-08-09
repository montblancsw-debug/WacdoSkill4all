<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('categorie_nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Exemple : Burgers',
                ],
            ])

            // Contenue de la Categorie :
            ->add('categorie_description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Description de la catégorie',
                ],
            ])

            ->add('categorie_nomdefichier', TextType::class, [
                'label' => 'Nom du fichier',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Exemple : burgers.jpg',
                ],
            ])

            ->add('categorie_image', TextType::class, [
                'label' => 'Image',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Exemple : burgers.jpg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Ce formulaire travaille directement avec l'entité Categorie.
            'data_class' => Categorie::class,
        ]);
    }
}
