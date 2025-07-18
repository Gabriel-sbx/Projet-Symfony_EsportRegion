<?php

namespace App\Controller;

use App\Entity\Plateform;
use App\Form\PlateformType;
use App\Repository\PlateformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Contrôleur des pages qui seront charger de la gestion des plateformes
 */
final class PlateformController extends AbstractController
{
     /**
     * Page avec la liste des plateformes
     * 
     * @route /plateform
     * @name app_plateform
     * @param PlateformRepository $plateformRepository (Service) Repository permettant l'accès aux données en base
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/plateform', name: 'app_plateform')]
    public function index(PlateformRepository $plateformRepository): Response
    {
        $objPlateforms = $plateformRepository->findAll();

        return $this->render('plateform/index.html.twig', [
            'plateforms' => $objPlateforms
        ]);
    }
    /**
     * Page de création d'une nouvelle plateforme
     * 
     * @route plateform/create
     * @name app_plateform_create
     * 
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * 
     * @return Response Réponse HTTP renvoyée au navigateur comportant le formulaire de création
     */
    #[Route('/plateform/create', name: 'app_plateform_create')]
    public function create(EntityManagerInterface $entityManager, 
        Request $request): Response
    {
        // Création d'un nouvel objet
        $objPlateforms = new Plateform();
        // Création du formulaire pour l'affichage
        // @param PlateformType : correspond à la classe du formulaire
        // @param $objPlateforms : l'objet qui sera remplit par le formulaire
        $formPlateformCreate = $this->createForm(PlateformType::class, $objPlateforms);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formPlateformCreate->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formPlateformCreate->isSubmitted() && $formPlateformCreate->isValid())
        {
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objPlateforms);
            // Enregistre les données en base, créer l'ID unique
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La plateforme a été créé avec succès"
            );
            return $this->redirectToRoute('app_plateform');
        }
        return $this->render('plateform/create.html.twig', [
            'formCreate'    => $formPlateformCreate,
            'plateforms'         => $objPlateforms
        ]);
    }
    /**
     * Route de suppression d'une plateforme
     * 
     * @route plateform/delete/{id}
     * @name app_plateform_delete
     * 
     * @param Plateform $objPlateforms Entité Plateform correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/plateform/delete/{id<\d+>}', name: 'app_plateform_delete')]
    public function delete(Plateform $objPlateforms, EntityManagerInterface $entityManager): Response
    {
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objPlateforms);
            // On lance la suppression en base
            $entityManager->flush();
            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_plateform');
        }
        catch(\Exception $exc) {

            // $this->addFlash(
            //     'error',
            //     $exc->getMessage()
            // );

            // Flash qui s'affichera à l'écran avec le message d'erreur 
            $this->addFlash(
                'danger',
                "La suppression à rencontrer un probleme verifier que tout les jeux ou tournois rattacher a cette plateforme on était délier"
            );            
            // Je redirige vers la page principale de plateform
            return $this->redirectToRoute('app_plateform');
        }
    }
    /**
     * Page  de modification d'une plateforme
     * 
     * @route plateform/edit/{id}
     * @name app_plateform_edit
     * 
     * @param Plateform $objPlateforms Entité Plateform correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'évènement
     */
    #[Route('/plateform/edit/{id<\d+>}', name: 'app_plateform_edit', methods: ['GET', 'POST'])]
    public function edit(Plateform $objPlateforms, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire pour l'affichage
        // @param PlateformType : correspond à la classe du formulaire
        // @param $objPlateforms : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formPlateformEdit = $this->createForm(PlateformType::class, $objPlateforms);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formPlateformEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formPlateformEdit->isSubmitted() && $formPlateformEdit->isValid())
        {     
            // Met à jour les données en base
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées"
            );
            return $this->redirectToRoute('app_plateform');
        }
        return $this->render('plateform/edit.html.twig', [
            'plateforms'         => $objPlateforms,
            'formEdit'      => $formPlateformEdit
        ]);
    }
}








