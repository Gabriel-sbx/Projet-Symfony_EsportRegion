<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NavLink
{
    // Correspond à la route de la page pour les liens
    public string $route;
    // Correspond au texte  de la page pour les liens
    public string $text;

}
