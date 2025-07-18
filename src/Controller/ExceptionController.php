<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des pages d'erreur personnalisées
 */
final class ExceptionController extends AbstractController
{
     /**
     * Affiche la page d'erreur par défault
     * 
     * @route /error
     * @name app_error
     * 
     * @return Response Réponse HTTP contenant la page d'erreur générique
     */
    #[Route('/error', name: 'app_error')]
    public function error(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig');
    }
    /**
     * Affiche la page d'erreur 404 (page non trouvée)
     * 
     * @route /error404
     * @name app_error404
     * 
     * @return Response Réponse HTTP contenant la page d'erreur 404
     */
    #[Route('/error404', name: 'app_error404')]
    public function error404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
}