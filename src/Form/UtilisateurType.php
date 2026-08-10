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

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Champ contenant le nom de l'utilisateur.
            ->add('utilisateur_nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Dupont',
                ],
            ])

            // Champ contenant le prénom de l'utilisateur.
            ->add('utilisateur_prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Jean',
                ],
            ])

            // Champ contenant l'adresse email.
            ->add('utilisateur_email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : utilisateur@wacdo.fr',
                ],
            ])

            // Champ temporaire utilisé pour saisir le mot de passe.
            ->add('plain_password', PasswordType::class, [
                'label' => $options['is_edit']
                    ? 'Nouveau mot de passe'
                    : 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'placeholder' => $options['is_edit']
                        ? 'Laisser vide pour conserver le mot de passe'
                        : 'Saisissez un mot de passe',
                ],
            ])

            // Liste des rôles disponibles dans le back-office.
            ->add('utilisateur_role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Préparateur' => 'ROLE_PREPARATEUR',
                    'Accueil' => 'ROLE_ACCUEIL',
                ],
                'placeholder' => 'Choisir un rôle',
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Lie le formulaire à l'entité Utilisateur.
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'is_edit' => false,
        ]);

        // Définit le type de l'option utilisée en modification.
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}