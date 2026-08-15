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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Les contraintes du mot de passe.
        $passwordConstraints = [
            new Assert\Length(
                min: 8,
                max: 72,
                minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
            ),
        ];

        // Rend le mot de passe obligatoire uniquement lors de la création.
        if (!$options['is_edit']) {
            array_unshift(
                $passwordConstraints,
                new Assert\NotBlank(
                    message: 'Le mot de passe est obligatoire.'
                )
            );
        }
        $builder

            // Champ obligatoire contenant le nom.
            ->add('utilisateur_nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Dupont',
                    'minlength' => 2,
                    'maxlength' => 255,
                ],
            ])

            // Champ facultatif contenant le prénom.
            ->add('utilisateur_prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Jean',
                    'maxlength' => 255,
                ],
            ])

            // Champ obligatoire contenant une adresse email complète.
            ->add('utilisateur_email', EmailType::class, [
                'label' => 'Adresse email',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : utilisateur@wacdo.fr',
                    'maxlength' => 255,
                    'autocomplete' => 'email',

                    // Exige un @, un domaine et une extension comme .fr ou .com.
                    'pattern' => '[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}',
                    // Aide le navigateur pour le format attendu.
                    'title' => 'Entrez une adresse complète, par exemple utilisateur@wacdo.fr',
                ],
            ])

            // Champ temporaire contenant le mot de passe.
            ->add('plain_password', PasswordType::class, [
                'label' => $options['is_edit']
                    ? 'Nouveau mot de passe'
                    : 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'constraints' => $passwordConstraints,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'minlength' => 8,
                    'maxlength' => 72,
                    'placeholder' => $options['is_edit']
                        ? 'Laisser vide pour conserver le mot de passe'
                        : 'Saisissez un mot de passe',
                ],
            ])

            // Uniquement les rôles autorisés.
            ->add('utilisateur_role', ChoiceType::class, [
                'label' => 'Rôle',
                'required' => true,
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

        // Définit le type de l'option utilisée.
        $resolver->setAllowedTypes(
            'is_edit',
            'bool'
        );
    }
}