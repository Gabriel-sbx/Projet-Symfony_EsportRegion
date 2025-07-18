<?php

namespace App\Controller;

use App\Entity\Game;

use App\Entity\Tournament;
use App\Form\TournamentType;
use Psr\Log\LoggerInterface;
use App\Service\FileUploader;
use App\Repository\PlateformRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MatchTournamentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
/**
 * Contrôleur des pages qui seront charger de la gestion et l'affichage concerner les tournois
 */
final class TournamentController extends AbstractController
{
     /**
     * Page d'index affichant la liste des tournois avec fonctionnalité de recherche
     * 
     * @route /tournament
     * @name app_tournament
     * 
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * @param PlateformRepository $plateformRepository (Service) Repository permettant l'accès aux données des plateformes
     * @param Request $request (dépendance) Objet contenant la requête envoyée par le navigateur ($_POST/$_GET)
     * 
     * @return Response Réponse HTTP renvoyée au navigateur avec la liste des tournois
     */
    #[Route('/tournament', name: 'app_tournament')]
    public function index(TournamentRepository $tournamentRepository, PlateformRepository $plateformRepository, Request $request): Response
    { 
        // On recupere la valeur du champ
        $inputKeyword = $request->query->get('keyword', '');
        //Si remplit alors t'éxecute la requete pour filtrer la recherche sinon tu garde la requete de base qui recupere tout 
        if ($inputKeyword) {
            $objTournaments = $tournamentRepository->findSearchKeyword($inputKeyword);
        } else {
            $objTournaments = $tournamentRepository->findAll();
        }
        $objPlateforms = $plateformRepository->findAll();
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentsController',
            'tournaments' => $objTournaments,
            'plateforms' => $objPlateforms,
            'inputKeyword' => $inputKeyword
        ]);
    }
    /**
     * Page affichant les détails d'un tournoi spécifique et ses matchs associés
     * 
     * @route /tournament/{id<\d+>}
     * @name app_tournament_show
     * 
     * @param Int $id Identifiant du tournoi à afficher
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * @param MatchTournamentRepository $matchTournamentRepository (Service) Repository permettant l'accès aux données des matchs
     * 
     * @return Response Réponse HTTP renvoyée au navigateur avec les détails du tournoi et ses matchs
     */
    #[Route('/tournament/{id<\d+>}', name: 'app_tournament_show')]
    public function show(Int $id, TournamentRepository $tournamentRepository, MatchTournamentRepository $matchTournamentRepository ): Response
    {
        $objTournaments= $tournamentRepository->find($id);
        $objMatchTournaments = $matchTournamentRepository->findBy(['tournaments' => $objTournaments]);
        /* On recupere l'utilisateur en session pour gerer l'affichage du bouton d'inscription ou désinscription si deja inscrit */
        $userSession=$this->getUser();
        $alreadyRegistered=false;
        if ($userSession) {
            // On verifie si la variable est pas null
            $alreadyRegistered = $objTournaments->getRegistration()->contains($userSession);
            /* Si quelqu'un de connecté on verifie avec l'objet du tournois qu'on souhaite 
            qui a une relation avec les utilisateurs getRegistration() qui retourne une collection d'objet qui represente les utilisateurs qui serait inscrit au tournois
            et avec contains on verifie si l'utilisateur fait partie de la collection d'objet si l'utilisateur et deja inscrit la méthode renverra true sinon false
            */ 
        }
        return $this->render('tournament/show.html.twig', [
                'user' => $userSession,
                'tournaments_id '=> $id,
                'oneTournaments' => $objTournaments,
                'matchTournaments' => $objMatchTournaments,
                'alreadyRegistered' => $alreadyRegistered
        ]);
    }
    /**
     * Méthode permettant à un utilisateur de s'inscrire à un tournoi
     * 
     * @route /tournament/{id<\d+>}/register
     * @name app_tournament_register
     * 
     * @param Int $id Identifiant du tournoi
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * 
     * @return Response Redirection vers la page de détails du tournoi ou vers la page de connexion
     */
    #[Route('/tournament/{id<\d+>}/register', name: 'app_tournament_register')]
    public function register(Int $id, TournamentRepository $tournamentRepository, EntityManagerInterface $entityManager): Response
    {
        /* On recupere l'utilisateur en session */
        $userSession=$this->getUser();
        if(!$userSession){
            $this->addFlash('warning','Vous devez être connecté pour vous inscrire à un tournoi');
            return $this->redirectToRoute('app_login');
        }
       
        $objTournaments= $tournamentRepository->find($id);
        // On verifie si l'utilisateur c'est deja inscrit
        if($objTournaments->getRegistration()->contains($userSession)){
            $this->addFlash('info','Vous êtes deja inscrit à ce tournoi');

        } else {
            // Si l'utilisateur n'est pas inscrit on ajoute l'utilisateur a la collection d'objet
            $objTournaments->addRegistration($userSession);
            $entityManager->persist($objTournaments);
            $entityManager->flush();
            $this->addFlash('success','Vous êtes inscrit avec succès à ce tournoi');
        }
        return $this->redirectToRoute('app_tournament_show', ['id' => $id]);
    }
    /**
     * Méthode permettant à un utilisateur de se désinscrire d'un tournoi
     * 
     * @route /tournament/{id<\d+>}/unregister
     * @name app_tournament_unregister
     * @param Int $id Identifiant du tournoi
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * 
     * @return Response Redirection vers la page de détails du tournoi ou vers la page de connexion
     */
    #[Route('/tournament/{id<\d+>}/unregister', name: 'app_tournament_unregister')]
    public function unregister(Int $id, TournamentRepository $tournamentRepository, EntityManagerInterface $entityManager): Response
    {
        /* On recupere l'utilisateur en session */
        $userSession=$this->getUser();
        if(!$userSession){
            $this->addFlash('warning','Vous devez être connecté ');
            return $this->redirectToRoute('app_login');
        }
        $objTournaments= $tournamentRepository->find($id);
        // On verifie si l'utilisateur c'est deja inscrit
        if($objTournaments->getRegistration()->contains($userSession)){
            // Si l'utilisateur est  inscrit on supprime sont inscription de la collection d'objet
            $objTournaments->removeRegistration($userSession);
            $entityManager->persist($objTournaments);
            $entityManager->flush();
            $this->addFlash('success','Désinscription du tournoi réussie');
        }
        return $this->redirectToRoute('app_tournament_show', ['id' => $id]);
    }
    /**
     * Page localisation
     * 
     * @route /location
     * @name app_tournament_location
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/location', name: 'app_tournament_location')]
    public function location(): Response
    {
        return $this->render('tournament/location.html.twig', [
            'controller_name' => 'TournamentsController',
        ]);
    }
    /**
     * Page administration des tournois
     * 
     * @route /administration-tournament
     * @name app_tournament_administration
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/administration-tournament', name: 'app_tournament_administration')]
    public function administration(): Response
    {
        return $this->render('tournament/administration.html.twig', [
            'controller_name' => 'administration',
        ]);
    }
    /**
     * Page liste des tournois
     * 
     * @route /tournament-list
     * @name app_tournament_list
     *  
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/administration-tournament/list', name: 'app_tournament_list')]

    public function list( TournamentRepository $tournamentRepository): Response
    { 
        $objTournaments = $tournamentRepository->findAll();
        return $this->render('tournament/list.html.twig', [
            'tournaments' => $objTournaments,
        ]);
    }
     /**
     * Page de création d'une nouveau Tournois
     * 
     * @route tournament/create
     * @name app_tournament_create
     * 
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * @param LoggerInterface $logger (dépendance) Service de journalisation des erreurs
     * @param FileUploader $fileUploader (dépendance) Service pour la gestion des fichiers image
     * 
     * @return Response Réponse HTTP renvoyée au navigateur comportant le formulaire de création
     */
    #[Route('/administration-tournament/create', name: 'app_tournament_create')]
    public function create(EntityManagerInterface $entityManager, 
        Request $request,   LoggerInterface $logger, FileUploader $fileUploader): Response
    {
        // Création d'un nouvel objet
        $objTournaments = new Tournament();
        $userSession=$this->getUser();
        // Création du formulaire pour l'affichage
        // @param TournamentType : correspond à la classe du formulaire
        // @param $objTournaments : l'objet qui sera remplit par le formulaire
        $formTournamentCreate = $this->createForm(TournamentType::class, $objTournaments);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formTournamentCreate->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formTournamentCreate->isSubmitted() && $formTournamentCreate->isValid())
        {   
            $pictureFile = $formTournamentCreate->get('img_card')->getData();
              // On vérifie si un fichier a été envoyé
            if($pictureFile) {
                try {
                    $newFilename = $fileUploader->upload($logger,$pictureFile, FileUploader::TOURNAMENT );
                    $objTournaments->setImgCard($newFilename);
                }
                catch(FileException $e) {
                    $logger->error($e->getMessage());;
                }
            }
            $objTournaments->setCreatedBy($userSession);
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objTournaments);
            // Enregistre les données en base, créer l'ID unique
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le tournois a été créé avec succès"
            );
            return $this->redirectToRoute('app_tournament_list');    
        }
        return $this->render('tournament/create.html.twig', [
            'formCreate'    => $formTournamentCreate,
            'tournaments'         => $objTournaments
        ]);
    }
     /**
     * Route de suppression d'un tournois
     * 
     * @route tounrament/delete/{id}
     * @name app_tournament_delete
     * 
     * @param Tournament $objTournaments Entité Tournament correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/administration-tournament/delete/{id<\d+>}', name: 'app_tournament_delete')]
    #[IsGranted('TOURNAMENT_DELETE', subject: 'objTournaments')]
    public function delete(Tournament $objTournaments, EntityManagerInterface $entityManager): Response
    {
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objTournaments);
            // On lance la suppression en base
            $entityManager->flush();
            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_tournament_list');
        }
        catch(\Exception $exc) {
            // Flash qui s'affichera à l'écran avec le message d'erreur de l'exception
            // $this->addFlash(
            //     'error',
            //     $exc->getMessage()
            // );
            $this->addFlash(
                'danger',
                "Vérifier que les matchs on était supprimé avant de supprimer ce tournoi"
            );
            return $this->redirectToRoute('app_tournament_list');
            // Je redirige vers la page principale de plateform
        }
    }
    /**
     * Page  de modification d'un tournois
     * 
     * @route administration-tournament/edit/{id}
     * @name app_tournament_edit
     * 
     * @param Tournament $objTournaments Entité Plateform correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * @param LoggerInterface $logger (dépendance) Service de journalisation des erreurs
     * @param FileUploader $fileUploader (dépendance) Service pour la gestion des fichiers image
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'évènement
     */
    #[Route('/administration-tournament/edit/{id<\d+>}', name: 'app_tournament_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TOURNAMENT_UPDATE', subject: 'objTournaments')]
    public function edit(Tournament $objTournaments, Request $request, EntityManagerInterface $entityManager,LoggerInterface $logger, FileUploader $fileUploader): Response
    {
        // Création du formulaire pour l'affichage
        // @param TournamentType : correspond à la classe du formulaire
        // @param $objTournaments : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formTournamentEdit = $this->createForm(TournamentType::class, $objTournaments);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formTournamentEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formTournamentEdit->isSubmitted() && $formTournamentEdit->isValid())
        {     
            $pictureFile = $formTournamentEdit->get('img_card')->getData();     
            // On vérifie si un fichier a été envoyé
            if($pictureFile) {
                $currentPictureFilename = $objTournaments->getImgCard();
                try {
                    if($currentPictureFilename != "") {
                        $fileUploader->remove($currentPictureFilename,FileUploader::TOURNAMENT); 
                    }
                    $newFilename = $fileUploader->upload($logger,$pictureFile, FileUploader::TOURNAMENT );
                    $objTournaments->setImgCard($newFilename);
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
            return $this->redirectToRoute('app_tournament_list');
        }
        return $this->render('tournament/edit.html.twig', [
            'tournaments'         => $objTournaments,
            'formEdit'      => $formTournamentEdit
        ]);
    }
}
