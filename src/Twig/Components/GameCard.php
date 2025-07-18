<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class GameCard
{
    public string  $plateform=""  ; // Plateforme du tournoi
    public string  $name="" ; // Nom du tournoi
    public string  $genre="" ; // Genre du tournoi (FPS,MMO...)
    public string  $imgSrcGames="" ; // Img du jeux du tournoi
    public string  $description=""; // Description du tournoi
}
