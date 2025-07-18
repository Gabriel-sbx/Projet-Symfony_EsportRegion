<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BootstrapToast
{
    public string $type=""; // Class pour le style de l'alerte dans le flash
    public string $text=""; // Texte pour l'alerte dans le flash
}
