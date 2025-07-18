<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use Psr\Log\LoggerInterface;
use App\Service\FileUploader;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
/**
 * Contrôleur des pages qui seront charger de la gestion des jeux 
 */
final class GameController extends AbstractController
{
    /**
     * Page avec la liste des jeux
     * 
     * @route /game
     * @name app_game
     * @param GameRepository $gameRepository (Service) Repository permettant l'accès aux données en base
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/game', name: 'app_game')]
    public function index(GameRepository $gameRepository): Response
    {
        $objGames = $gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $objGames
        ]);
    }
     /**
     * Page de création d'une nouveau jeux
     * 
     * @route game/create
     * @name app_game_create
     * 
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * @param LoggerInterface $logger (dépendance) Service de journalisation des erreurs
     * @param FileUploader $fileUploader (dépendance) Service pour la gestion des fichiers image
     * 
     * @return Response Réponse HTTP renvoyée au navigateur comportant le formulaire de création
     */
    #[Route('/game/create', name: 'app_game_create')]
    public function create(EntityManagerInterface $entityManager, 
        Request $request, LoggerInterface $logger, FileUploader $fileUploader): Response
    {
        // Création d'un nouvel objet
        $objGames = new Game();
        // Création du formulaire pour l'affichage
        // @param GameType : correspond à la classe du formulaire
        // @param $objGames : l'objet qui sera remplit par le formulaire
        $formGameCreate = $this->createForm(GameType::class, $objGames);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formGameCreate->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formGameCreate->isSubmitted() && $formGameCreate->isValid())
        {
            $pictureFile = $formGameCreate->get('img')->getData();
            // On vérifie si un fichier a été envoyé
            if($pictureFile) {
                try {
                    $newFilename = $fileUploader->upload($logger, $pictureFile, FileUploader::GAME);
                    $objGames->setImg($newFilename);
                }
                catch(FileException $e) {
                    $logger->error($e->getMessage());;
                }
            }
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objGames);
            // Enregistre les données en base, créer l'ID unique
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le jeux a été créé avec succès"
            );
            return $this->redirectToRoute('app_game');
        }
        return $this->render('game/create.html.twig', [
            'formCreate'    => $formGameCreate,
            'plateforms'         => $objGames
        ]);
    }
    /**
     * Route de suppression d'un jeux
     * 
     * @route game/delete/{id}
     * @name app_game_delete
     * 
     * @param Game $objGames Entité Game correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/game/delete/{id<\d+>}', name: 'app_game_delete')]
    public function delete(Game $objGames, EntityManagerInterface $entityManager): Response
    {
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objGames);

            // On lance la suppression en base
            $entityManager->flush();

            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_game');
        }
        catch(\Exception $exc) {

            // Flash qui s'affichera à l'écran avec le message d'erreur de l'exception
            $this->addFlash(
                'error',
                $exc->getMessage()
            );
            return $this->redirectToRoute('app_game');

            // Je redirige vers la page principale de plateform
        }
    }
    /**
     * Page  de modification d'une plateforme
     * 
     * @route game/edit/{id}
     * @name app_game_edit
     * 
     * @param Game $objGames Entité Game correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * @param LoggerInterface $logger (dépendance) Service de journalisation des erreurs
     * @param FileUploader $fileUploader (dépendance) Service pour la gestion des fichiers image
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification du jeux
     */
    #[Route('/game/edit/{id<\d+>}', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Game $objGames, Request $request, EntityManagerInterface $entityManager ,LoggerInterface $logger, FileUploader $fileUploader): Response
    {
        // Création du formulaire pour l'affichage
        // @param GameType : correspond à la classe du formulaire
        // @param $objGames : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formGameEdit = $this->createForm(GameType::class, $objGames);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formGameEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formGameEdit->isSubmitted() && $formGameEdit->isValid())
        {     
            $pictureFile = $formGameEdit->get('img')->getData();
            // On vérifie si un fichier a été envoyé
            if($pictureFile) {
                $currentPictureFilename = $objGames->getImg();
                try {
                    if($currentPictureFilename != "") {
                        $fileUploader->remove($currentPictureFilename, FileUploader::GAME); 
                    }
                    $newFilename = $fileUploader->upload($logger, $pictureFile, FileUploader::GAME);
                    $objGames->setImg($newFilename);
                }
                catch(FileException $e) {
                    $logger->error($e->getMessage());;
                }
            }
            // Met à jour les données en base
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées"
            );
            return $this->redirectToRoute('app_game');
        }
        return $this->render('game/edit.html.twig', [
            'games'         => $objGames,
            'formEdit'      => $formGameEdit
        ]);
    }
}
