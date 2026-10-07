<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UtilisateurType extends AbstractType
{

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

    // Construit le formulaire de création et de modification d'un utilisateur.
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {

    // Définit les règles de validation du mot de passe.
        $passwordConstraints =
        [
            new Assert\Length
            (
                min: 8,
                max: 72,
                minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
            ),
        ];

// Rend le mot de passe obligatoire uniquement lors de la création.
        if (!$options['is_edit'])
        {
            array_unshift($passwordConstraints,new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'));
        }
        $builder

// Ajoute le champ du nom de l'utilisateur.
            ->add('utilisateur_nom', TextType::class,
            [
                'label' => 'Nom',
                'required' => true,
                'attr' =>
                ['class' => 'form-control','placeholder' => 'Ex : Dupont','minlength' => 2,'maxlength' => 255,],
            ])

// Ajoute le champ du prénom de l'utilisateur.
            ->add('utilisateur_prenom', TextType::class,
            [
                'label' => 'Prénom',
                'required' => false,
                'attr' =>['class' => 'form-control','placeholder' => 'Ex : Jean','maxlength' => 255,],
            ])

// Ajoute le champ de l'adresse email.
            ->add('utilisateur_email', EmailType::class,
            [
                'label' => 'Adresse email',
                'required' => true,
                'attr' =>
                [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : utilisateur@wacdo.fr',
                    'maxlength' => 255,
                    'autocomplete' => 'email',
                    'pattern' => '[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}',
                    'title' => 'Entrez une adresse complète, par exemple utilisateur@wacdo.fr',
                ],
            ])

            // Ajoute le champ du mot de passe.
            ->add('plain_password', PasswordType::class,
            [
                'label' => $options['is_edit']? 'Nouveau mot de passe': 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'constraints' => $passwordConstraints,
                'attr' =>
                [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'minlength' => 8,
                    'maxlength' => 72,
                    'placeholder' => $options['is_edit']? 'Laisser vide pour conserver le mot de passe': 'Saisissez un mot de passe',
                ],
            ])

            // Ajoute le choix du rôle de l'utilisateur.
            ->add('utilisateur_role', ChoiceType::class,
            [
                'label' => 'Rôle',
                'required' => true,
                'choices' =>
                [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Préparateur' => 'ROLE_PREPARATEUR',
                    'Accueil' => 'ROLE_ACCUEIL',
                    'Client' => 'ROLE_USER',
                ],
                'placeholder' => 'Choisir un rôle',
                'attr' => ['class' => 'form-select',],
            ]);
    }

    // Configure l'entité utilisée et le mode création ou modification.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class,'is_edit' => false,]);
        // Vérifie que l'option is_edit contient une valeur booléenne.
        $resolver->setAllowedTypes('is_edit','bool');
    }
}