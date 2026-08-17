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
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('menu_nom', TextType::class, [
                'label' => 'Nom du menu',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Menu Wacdo',
                ],
            ])

            ->add('menu_description', CKEditorType::class, [
                'label' => 'Description',
                'required' => false,
                'config_name' => 'main_config',
            ])

            ->add('menu_prix', NumberType::class, [
                'label' => 'Prix (€)',
                'required' => true,
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => 'Ex : 12.90',
                ],
            ])

            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'produit_nom',
                'label' => 'Produits du menu',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'by_reference' => false,
            ])

            ->add('imageFile', FileType::class, [
                'label' => 'Image du menu',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
            ])

            ->add('menu_status', CheckboxType::class, [
                'label' => 'Menu actif',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}

