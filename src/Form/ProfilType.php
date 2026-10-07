<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProfilType extends AbstractType
{

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Construit le formulaire de modification du profil.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Nom de l'utilisateur.
            ->add('utilisateur_nom', TextType::class,
            [
                'label' => 'Nom',
                'attr' =>
                [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom',
                ],
            ])

            // Prénom de l'utilisateur.
            ->add('utilisateur_prenom', TextType::class,
            [
                'label' => 'Prénom',
                'required' => false,
                'attr' =>
                [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom',
                ],
            ])

            // Adresse email.
            ->add('utilisateur_email', EmailType::class,
            [
                'label' => 'Adresse email',
                'attr' =>
                [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : utilisateur@wacdo.fr',
                ],
            ])

            // Nouveau mot de passe.
            ->add('plain_password', RepeatedType::class,
            [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'constraints' =>
                [
                    new Assert\Length(
                    min: 8,
                    max: 72,
                    minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'),
                ],
                'first_options' =>
                [
                    'label' => 'Nouveau mot de passe',
                    'attr' =>
                    [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password',
                        'minlength' => 8,
                        'maxlength' => 72,
                        'placeholder' => 'Laisser vide pour conserver le mot de passe',
                    ],
                ],
                'second_options' =>
                [
                    'label' => 'Confirmer le mot de passe',
                    'attr' =>
                    [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password',
                        'minlength' => 8,
                        'maxlength' => 72,
                        'placeholder' => 'Confirmez le nouveau mot de passe',
                    ],
                ],
            ]);
    }

    // Associe le formulaire à l'entité Utilisateur.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class,]);
    }
}