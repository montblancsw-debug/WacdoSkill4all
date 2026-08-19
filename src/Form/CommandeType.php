<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder

            // Permet de choisir le statut.
            ->add(
                'commande_statut',
                ChoiceType::class,
                [
                    'label' => 'Statut',
                    'required' => true,

                    'choices' => [
                        'En attente' => 'en attente',
                        'En préparation' => 'en préparation',
                        'Prête' => 'prête',
                        'En livraison' => 'en livraison',
                        'Livrée' => 'livrée',
                        'Annulée' => 'annulée',
                    ],

                    'placeholder' =>
                        'Choisir un statut',

                    'attr' => [
                        'class' => 'form-select',
                    ],
                ]
            )

            // Permet de renseigner la date et l'heure.
            ->add(
                'commande_date',
                DateTimeType::class,
                [
                    'label' => 'Date et heure',
                    'required' => true,
                    'widget' => 'single_text',

                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )

            // Affiche le total calculé.
            ->add(
                'commande_total',
                NumberType::class,
                [
                    'label' => 'Total (€)',
                    'required' => false,
                    'input' => 'string',
                    'scale' => 2,
                    'html5' => true,
                    'disabled' => true,

                    'help' =>
                        'Le total est calculé automatiquement à partir des articles.',

                    'attr' => [
                        'class' => 'form-control',
                        'step' => '0.01',
                        'min' => '0',
                    ],
                ]
            )

            // Sélectionne l'utilisateur associé.
            ->add(
                'utilisateur',
                EntityType::class,
                [
                    'class' => Utilisateur::class,

                    'choice_label' => function (
                        Utilisateur $utilisateur
                    ): string {
                        $identite = trim(
                            ($utilisateur->getUtilisateurPrenom() ?? '')
                            . ' '
                            . ($utilisateur->getUtilisateurNom() ?? '')
                        );

                        return $identite
                            . ' - '
                            . $utilisateur->getUtilisateurEmail();
                    },

                    'label' => 'Utilisateur',
                    'placeholder' => 'Aucun utilisateur',
                    'required' => false,

                    'attr' => [
                        'class' => 'form-select',
                    ],
                ]
            );
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults(
            [
                'data_class' => Commande::class,
            ]
        );
    }
}