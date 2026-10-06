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
    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Construit le formulaire de création et de modification d'une commande.

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder

            // Ajoute le choix du statut de la commande.

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

                    'placeholder' => 'Choisir un statut',

                    // L'accueil ne modifie pas directement l'avancement depuis le formulaire d'édition.

                    'disabled' => $options['status_disabled'],
                    'attr' => [
                        'class' => 'form-select',
                    ],
                ]
            )

            // Ajoute la date et l'heure de création de la commande.

            ->add(
                'commande_date',
                DateTimeType::class,
                [
                    'label' => 'Date et heure de la commande',
                    'required' => true,
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )

            // Ajoute l'heure prévue pour la livraison.

            ->add(
                'commande_horaire_livraison',
                DateTimeType::class,
                [
                    'label' => 'Livraison prévue',
                    'required' => true,
                    'widget' => 'single_text',
                    'html5' => true,
                    'help' => 'Indiquez la date et l\'heure prévues pour la remise de la commande.',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )

            // Affiche le total calculé automatiquement.

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
                    'help' => 'Le total est calculé automatiquement à partir des articles.',
                    'attr' => [
                        'class' => 'form-control',
                        'step' => '0.01',
                        'min' => '0',
                    ],
                ]
            )

            // Ajoute le choix de l'utilisateur associé à la commande.

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

    // Configure l'entité utilisée et les options du formulaire.

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults(
            [
                'data_class' => Commande::class,
                'status_disabled' => false,
            ]
        );

        $resolver->setAllowedTypes(
            'status_disabled',
            'bool'
        );
    }
}