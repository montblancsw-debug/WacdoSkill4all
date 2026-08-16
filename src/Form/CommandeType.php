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
        $commande = $options['data'];

        $isEdit = $commande instanceof Commande
            && $commande->getId() !== null;

        $builder

            // Permet de choisir le statut.
            ->add('commande_statut', ChoiceType::class, [
                'label' => 'Statut',
                'required' => true,
                'choices' => [
                    'En attente' => 'en attente',
                    'Préparée' => 'préparée',
                    'Livrée' => 'livrée',
                ],
                'placeholder' => 'Choisir un statut',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            // Permet de renseigner la date et l'heure.
            ->add('commande_date', DateTimeType::class, [
                'label' => 'Date et heure',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            // Affiche le montant total de la commande.
            ->add('commande_total', NumberType::class, [
                'label' => 'Total (€)',
                'required' => true,
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'disabled' => $isEdit,
                'help' => $isEdit
                    ? 'Le total est calculé automatiquement à partir des articles.'
                    : null,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => 'Ex : 19.50',
                ],
            ])

            // Permet de sélectionner l'utilisateur associé.
            ->add('utilisateur', EntityType::class, [
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
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}