<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\TournamentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Contrôleur de la page d'accueil et des pages statiques
 */
final class HomeController extends AbstractController
{
    /**
     * Page d'accueil de l'application
     * Cette page sera accessible par défaut lorsque l'on arrive sur le site
     * 
     * @route /
     * @name app_home
     * 
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données en base
     * @param GameRepository $gameRepository (Service) Repository permettant l'accès aux données en base
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/', name: 'app_home')]
    public function index(TournamentRepository $tournamentRepository, GameRepository $gameRepository  ): Response
    {
        $objTournaments = $tournamentRepository->findAll();
        $objGames = $gameRepository->findBySixGames();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tournaments' => $objTournaments,
            'games' => $objGames
        ]);
    }
    /**
     * Page à propos de l'application
     * 
     * @route /about
     * @name app_about
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    /**
     * Page contact de l'application
     * 
     * @route /contact
     * @name app_contact
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
