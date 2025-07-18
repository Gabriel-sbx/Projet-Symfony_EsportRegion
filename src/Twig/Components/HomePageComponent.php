<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class HomePageComponent
{
    // Donnée pour le hero de la page Home 
    public string $effectWowDelay =""; // Temp pour l'effet d'apparition
    public string $imgSrcHero =""; // Image pour la section
    public string $titleHero ="";  // Titre de la section
    public string $descriptionHero =""; // Description de la section


}
