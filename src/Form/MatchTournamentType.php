<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Tournament;
use App\Entity\MatchTournament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchTournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Pour récuperer l'option quon a ajouter dans le controller contenant l'objet Tournaments
        $tournament = $options['tournament'];
        // Récuperer seulement les utilisateurs qui sont inscrit dans le tournois
        $participantsTournament = $tournament->getRegistration();


        $builder
            ->add('round',null,[
                'label' => 'Etape du match :',
            ])
            ->add('groupe_participant_1', EntityType::class, [
                'class' => User::class,                
                'choice_label' => 'pseudo',
                'choices' => $participantsTournament,
                'label' => 'Choisir un participant :',                
                // Pour pas récuperer l'objet user en entier car on veut recuperer le pseudo de celui ci et le convertir en string 
                'mapped' => false,
            ])

            ->add('groupe_participant_2', EntityType::class, [
                'class' => User::class,
                'choices' => $participantsTournament,
                'choice_label' => 'pseudo',
                'label' => 'Choisir un participant :',
                'mapped' => false,
            ])

            ->add('scores',null,[
                'label' => 'Score :',
            ])
            ->add('date', null, [
                'label' => 'Date exacte du match :',

                'widget' => 'single_text',
            ])
            ->add('description',null,[
                'label' => 'Description du match :',
            ])
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'name',
                'label' => 'Choisir un jeu :'

            ])
            ->add('tournaments', EntityType::class, [
                'class' => Tournament::class,
                'choice_label' => 'name',
                'label' => 'Tournoi sélectionné',
                'disabled' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchTournament::class,
            'tournament' => null,

        ]);
    }
}
