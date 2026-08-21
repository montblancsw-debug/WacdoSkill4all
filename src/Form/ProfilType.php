<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

// Construit le formulaire de modification du profil.

    public function buildForm(FormBuilderInterface $builder,array $options): void {
        $builder

// Ajoute le champ permettant de modifier l'adresse email.

            ->add('utilisateur_email', EmailType::class, [
                'label' => 'Adresse email',

                'attr' => ['class' => 'form-control','placeholder' => 'Ex : utilisateur@wacdo.fr',],
            ])

// Ajoute les champs permettant de modifier et confirmer le mot de passe.

            ->add('plain_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',

                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['class' => 'form-control','autocomplete' => 'new-password','placeholder' => 'Laisser vide pour conserver le mot de passe',],
                ],

                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['class' => 'form-control','autocomplete' => 'new-password','placeholder' => 'Confirmez le nouveau mot de passe',],
                ],
            ]);
    }

// Associe le formulaire à l'entité Utilisateur.

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}