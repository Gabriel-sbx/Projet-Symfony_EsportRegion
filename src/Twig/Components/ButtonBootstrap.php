<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ButtonBootstrap
{
    public string  $route=""; // Route pour le bouton
    public string  $text=""; // Texte pour le bouton

}
