<?php

namespace App\Form;

use App\Entity\Plateform;
use App\Entity\Tournament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom :'
                ])
            ->add('description', null, [
                'label' => 'Description :'
                ])
            ->add('img_card', FileType::class, [
                'label' => 'Image de la fiche :',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ]
                    ])
                ]
                ])
            ->add('limit_player', null, [
                'label' => 'Limite de joueur :'
                ])
            ->add('registration_open', null, [
                'label' => 'Cochez si complet'
                ])
            ->add('date_start', null, [
                'widget' => 'single_text',
                'label' => 'Date de debut :'

            ])
            ->add('date_end', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin :'

            ])
            ->add('plateforms', EntityType::class, [
                'class' => Plateform::class,
                'label' => 'Plateforme principale du tournois :',

                'choice_label' => function(Plateform $objPlateforms): string {
                    return $objPlateforms->getName();
                }
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
