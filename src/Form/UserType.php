<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Votre E-mail : :'
                ])
            // ->add('roles', null, [
            //     'label' => 'Roles :'
            //     ])
           
            ->add('isVerified', null, [
                'label' => 'Compte vérifier :',
                'disabled' => true

                ])
            ->add('name', null, [
                'label' => 'Nom :'
                ])
            ->add('surname', null, [
                'label' => 'Prénom :'
                ])
            ->add('pseudo', null, [
                'label' => 'Pseudo :'
            ]);
            if ($options['show_password_field']) {

            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe :',

                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} carctères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'show_password_field' => true, // Par défaut, afficher le champ
        ]);
    }
}
