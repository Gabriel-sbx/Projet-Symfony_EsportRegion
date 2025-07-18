<?php

namespace App\Controller;

use App\Entity\Tournament;

use App\Entity\MatchTournament;
use App\Form\MatchTournamentType;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MatchTournamentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Contrôleur des pages qui seront charger de la gestion des matchs 
 */
final class MatchTournamentController extends AbstractController
{
    /**
     * Page liste des tournois pour ajoutez des match a ceux ci
     * 
     * @route /match-tournament/list
     * @name app_tournament_list
     * 
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/match-tournament/list-tournament', name: 'app_match_tournament_list_tournament')]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        $objTournaments = $tournamentRepository->findAll();
        return $this->render('match_tournament/index.html.twig', [
            'tournaments' => $objTournaments,
        ]);
    }
      /**
     * Page liste des matchs du tournois
     * 
     * @route /match-tournament/list
     * @name app_tournament_list
     * 
     * @param Int $id Identifiant du tournoi pour récuperer la liste des matchs de celui-ci
     * @param TournamentRepository $tournamentRepository (Service) Repository permettant l'accès aux données des tournois
     * @param MatchTournamentRepository $matchTournamentRepository (Service) Repository permettant l'accès aux données des matchs
     * 
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/match-tournament/list-match/{id<\d+>}', name: 'app_match_tournament_list_match')]
    public function listMatchTournament(Int $id, TournamentRepository $tournamentRepository, MatchTournamentRepository $matchTournamentRepository): Response
    {
        $objTournaments = $tournamentRepository->find($id);
        // On met à jour l'objet avec les inscription de celui ci 
        $objRegistrations= $tournamentRepository->find($id)->getRegistration()->toArray();
        // Récuperer les matchs du tournois
        $objMatchTournaments = $matchTournamentRepository->findBy(['tournaments' => $objTournaments]);
        return $this->render('match_tournament/list.html.twig', [
            'tournaments' => $objTournaments,
            'regsitrations' => $objRegistrations,

            'matchs' => $objMatchTournaments,
        ]);
    }
    /**
     * Page de création d'une nouveau match
     * 
     * @route match-tournament/create
     * @name app_match_tournament_create
     * 
     * @param Int $id Identifiant du tournoi 
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * @param Tournament $objTournaments (dépendance) Objet du tournois pour liée les matchs au tournois
     * 
     * @return Response Réponse HTTP renvoyée au navigateur comportant le formulaire de création
     */
    #[Route('/match-tournament/create/{id<\d+>}', name: 'app_match_tournament_create')]
    public function create(Int $id, EntityManagerInterface $entityManager, 
        Request $request, Tournament $objTournaments): Response
    {
        // Création d'un nouvel objet
        $objMatchTournaments = new MatchTournament();
        $objMatchTournaments->setTournaments($objTournaments);
        // Création du formulaire pour l'affichage
        // @param MatchTournamentType : correspond à la classe du formulaire
        // @param $objMatchTournaments : l'objet qui sera remplit par le formulaire
        $formMatchTournamentCreate = $this->createForm(MatchTournamentType::class, $objMatchTournaments, [
            'tournament' => $objTournaments
        ]);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formMatchTournamentCreate->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formMatchTournamentCreate->isSubmitted() && $formMatchTournamentCreate->isValid())
        {
            // Récupérer les objets User sélectionnés
            // le get() accéde au champ name du formulaire et getData() récupere la donnée de celui ci
            $user1 = $formMatchTournamentCreate->get('groupe_participant_1')->getData();
            $user2 = $formMatchTournamentCreate->get('groupe_participant_2')->getData();
            // Extraire et définir les pseudos comme chaînes de caractères pour éviter une nouvelle relation et preserver le match meme si il supprime sont compte 
            // Ici on met à jour les champs setGroupeParticipant1() et setGroupeParticipant2() dans notre  objMatchTournaments 
            // ou on donne en parametre notre objet utilisateurs avec toute ces info et on extrait seulement sont pseudo 
            $objMatchTournaments->setGroupeParticipant1($user1->getPseudo());
            $objMatchTournaments->setGroupeParticipant2($user2->getPseudo());
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objMatchTournaments);
            // Enregistre les données en base, créer l'ID unique
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le match a été créé avec succès"
            );
            return $this->redirectToRoute('app_match_tournament_list_match', ['id' => $id]);
        }
        return $this->render('match_tournament/create.html.twig', [
            'formCreate'    => $formMatchTournamentCreate,
            'tournaments'   => $objTournaments,
            'id'            => $id,

        ]);
    }
    /**
     * Page  de modification d'un match
     * 
     * @route /match-tournament/edit/{tournament_id<\d+>}/{id<\d+>}
     * @name app_match_tournament_edit
     * 
     * @param MatchTournament $objMatchTournaments Entité MatchTournament correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'évènement
     */
    #[Route('/match-tournament/edit/{id<\d+>}', name: 'app_match_tournament_edit', methods: ['GET', 'POST'])]
    public function edit( MatchTournament $objMatchTournaments,  Request $request, EntityManagerInterface $entityManager): Response
    {
        $objTournaments= $objMatchTournaments->getTournaments();
        // Création du formulaire pour l'affichage
        // @param MatchTournamentType : correspond à la classe du formulaire
        // @param $objMatchTournaments : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formMatchTournamentEdit = $this->createForm(MatchTournamentType::class, $objMatchTournaments, [
            'tournament' => $objTournaments
        ]);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formMatchTournamentEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formMatchTournamentEdit->isSubmitted() && $formMatchTournamentEdit->isValid())
        {     
            // Récupérer les objets User sélectionnés
            // le get() accéde au champ name du formulaire et getData() récupere la donnée de celui ci
            $user1 = $formMatchTournamentEdit->get('groupe_participant_1')->getData();
            $user2 = $formMatchTournamentEdit->get('groupe_participant_2')->getData();
            // Extraire et définir les pseudos comme chaînes de caractères pour éviter une nouvelle relation et preserver le match meme si il supprime sont compte 
            // Ici on met à jour les champs setGroupeParticipant1() et setGroupeParticipant2() dans notre  objMatchTournaments 
            // ou on donne en parametre notre objet utilisateurs avec toute ces info et on extrait seulement sont pseudo 
            $objMatchTournaments->setGroupeParticipant1($user1->getPseudo());
            $objMatchTournaments->setGroupeParticipant2($user2->getPseudo());
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objMatchTournaments);
            // Met à jour les données en base
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées"
            );
            return $this->redirectToRoute('app_match_tournament_list_match', ['id' => $objTournaments->getId()]);
        }
        return $this->render('match_tournament/edit.html.twig', [
            'tournaments'   => $objTournaments,
            'formEdit'      => $formMatchTournamentEdit
        ]);
    } 
    /**
     * Route de suppression d'un match
     * 
     * @route tounrament/delete/{id}
     * @name app_tournament_delete
     * 
     * @param MatchTournament $objMatchTournaments MatchTournament Tournament correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/match-tournament/delete/{id<\d+>}', name: 'app_match_tournament_delete')]
    public function delete( MatchTournament $objMatchTournaments, EntityManagerInterface $entityManager): Response
    {
        $objTournaments= $objMatchTournaments->getTournaments();
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objMatchTournaments);
            // On lance la suppression en base
            $entityManager->flush();
            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_match_tournament_list_match', ['id' => $objTournaments->getId()]);
        }
        catch(\Exception $exc) {
            // Flash qui s'affichera à l'écran avec le message d'erreur de l'exception
            $this->addFlash(
                'error',
                $exc->getMessage()
            );           
            // Je redirige vers la page principale de plateform
            return $this->redirectToRoute('app_match_tournament_list_match', ['id' => $objTournaments->getId()]);
        }
    }
}



