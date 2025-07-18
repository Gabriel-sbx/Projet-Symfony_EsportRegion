<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TournamentCard
{
    public string  $plateform=""  ; // Correspond à la plateforme du tournois 
    public string  $name="" ; // Correspond au nom du tournois 
    public string  $date_start="" ; // Correspond à la date de commencement du tournois 
    public string  $date_end="";  // Correspond à la date de fin du tournois   
    public int     $limitPlayer=0 ; // Correspond à la limite de joueur du tournois 
    public string  $imgSrcTournaments="" ;// Correspond à l'image' du tournois 
    public string  $route=""; // Correspond à la route du tournois 
}
