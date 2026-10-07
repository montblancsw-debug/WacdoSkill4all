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

class RegistrationFormType extends AbstractType
{

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

    // Construit le formulaire d'inscription d'un nouveau client.
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {
        $builder

            // Ajoute le champ du nom.
            ->add('utilisateur_nom', TextType::class,
            [
                'label' => 'Nom',
                'required' => true,
                'attr' =>['class' => 'form-control','placeholder' => 'Ex : Dupont','minlength' => 2,'maxlength' => 255,],
            ])

            // Ajoute le champ du prénom.
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
                    'placeholder' => 'Ex : jean@exemple.fr',
                    'autocomplete' => 'email',
                    'maxlength' => 255,
                    'pattern' => '[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}',
                ],
            ])

            // Ajoute et confirme le mot de passe du nouveau compte.
            ->add('plain_password', RepeatedType::class,
            [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => true,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                // Vérifie que le mot de passe est renseigné et suffisamment long.
                'constraints' =>
                [
                    new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                    new Assert\Length
                    (
                        min: 8,
                        max: 72,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
                'first_options' => ['label' => 'Mot de passe','attr' => ['class' => 'form-control','autocomplete' => 'new-password','minlength' => 8,'maxlength' => 72,],],
                'second_options' => ['label' => 'Confirmer le mot de passe','attr' => ['class' => 'form-control','autocomplete' => 'new-password','minlength' => 8,'maxlength' => 72,],
                ],
            ]);
    }

    // Associe le formulaire à l'entité Utilisateur.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class,]);
    }
}