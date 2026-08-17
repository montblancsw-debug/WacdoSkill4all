<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('menu_nom')
            ->add('menu_description')
            ->add('menu_prix')
            ->add('menu_status')
            ->add('menu_image')
            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'produit_nom',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
