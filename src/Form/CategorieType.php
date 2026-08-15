<?php

namespace App\Form;

use App\Entity\Categorie;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ obligatoire contenant le nom de la catégorie.
            ->add('categorie_nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Exemple : Burgers',
                    'minlength' => 2,
                    'maxlength' => 255,
                ],
            ])

            // Utilise CKEditor pour la description facultative.
            ->add('categorie_description', CKEditorType::class, [
                'label' => 'Description',
                'required' => false,
                'config_name' => 'main_config',
            ])

            // Permet de sélectionner une image.
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Lie le formulaire à l'entité Categorie.
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}