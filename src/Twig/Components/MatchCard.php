<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class MatchCard
{
    public string  $scores=""  ; // Scores du match
    public string  $game="" ; // Jeux du match
    public string  $participant_1="" ; // Partcipant numéro un du match   
    public string  $participant_2=""; // Partcipant numéro deux du match   
    public int     $round=0 ; // Etape du match   
    public string  $date="" ; //Date du match
    public string  $description=""; //Description du match
}
