<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BootstrapModal
{
    public string $modalId=""; // Correspond à l'identifiant pour l'unicité des champ de la modale pour le javascript pour le changement de roles
    public string $title=""; // Correspond au titre  de la modale pour le changement de roles
    public string $formAction="";// Correspond l'action pour le formulaire 
}
