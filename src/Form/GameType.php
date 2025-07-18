<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Plateform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du jeux :'
                ])
            ->add('genre', null, [
                'label' => 'Genre du jeux :'
                ])
            ->add('description', null, [
                'label' => 'Description du jeux :'
                ])
            ->add('img', FileType::class, [
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
            ->add('plateforms', EntityType::class, [
                'class' => Plateform::class,
                'label' => 'Plateforme du jeux :',
                'choice_label' => function(Plateform $objPlateforms): string {
                    return $objPlateforms->getName();
                }
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
