<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/**
 * Contrôleur des pages qui seront charger de la gestion des utilisateurs
 */
final class UserController extends AbstractController
{
    /**
     * Page affichant la liste de tous les utilisateurs
     * 
     * @route /user
     * @name app_user
     * @param UserRepository $userRepository (Service) Repository permettant l'accès aux données des utilisateurs
     * 
     * @return Response Réponse HTTP renvoyée au navigateur avec la liste des utilisateurs
     */
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $objUsers = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $objUsers
        ]);
    }
     /**
     * Page de création d'une nouvelle utilisateurs
     * 
     * @route user/create
     * @name app_user_create
     * 
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param UserPasswordHasherInterface $userPasswordHasher (dépendance) Service de hachage des mots de passe
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     * 
     * @return Response Réponse HTTP renvoyée au navigateur comportant le formulaire de création
     */
    #[Route('/user/create', name: 'app_user_create')]
    public function create(EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher, 
        Request $request): Response
    {
        // Création d'un nouvel objet
        $objUsers = new User();
        // Création du formulaire pour l'affichage
        // @param UserType : correspond à la classe du formulaire
        // @param $objUsers : l'objet qui sera remplit par le formulaire
        $formUserCreate = $this->createForm(UserType::class, $objUsers);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formUserCreate->handleRequest($request);

        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formUserCreate->isSubmitted() && $formUserCreate->isValid())
        {
             /** @var string $plainPassword */
             $passwordHash = $formUserCreate->get('password')->getData();
             // encode the plain password
             $objUsers->setPassword($userPasswordHasher->hashPassword($objUsers, $passwordHash));
            // Prépare les données à être sauvegardées en base
            $entityManager->persist($objUsers);
            // Enregistre les données en base, créer l'ID unique
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur à été créé avec succès"
            );
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/create.html.twig', [
            'formCreate'    => $formUserCreate,
            'users'         => $objUsers
        ]);
    }
    /**
     * Route de suppression d'un utilisateur
     * 
     * @route user/delete/{id}
     * @name app_user_delete
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/user/delete/{id<\d+>}', name: 'app_user_delete')]
    public function delete(User $objUsers, EntityManagerInterface $entityManager): Response
    {
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objUsers);
            // On lance la suppression en base
            $entityManager->flush();
            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_user');
        }
        catch(\Exception $exc) {
            // Flash qui s'affichera à l'écran avec le message d'erreur de l'exception
            $this->addFlash(
                'error',
                $exc->getMessage()
            );            
            // Je redirige vers la page principale de plateform
            return $this->redirectToRoute('app_user');
        }
    }
    /**
     * Page  de modification d'un utilisateur
     *
     * @route user/edit/{id}
     * @name app_user_edit
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'utilisateur
     */
    #[Route('/user/edit/{id<\d+>}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(User $objUsers, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire pour l'affichage
        // @param PlateformType : correspond à la classe du formulaire
        // @param $objPlateforms : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formUserEdit = $this->createForm(UserType::class, $objUsers,[
            'show_password_field' => false
        ]);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formUserEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formUserEdit->isSubmitted() && $formUserEdit->isValid())
        {     
            // Met à jour les données en base
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées"
            );
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/edit.html.twig', [
            'users'         => $objUsers,
            'formEdit'      => $formUserEdit
        ]);
    }
     /**
     * Page  de modification des rôles d'un utilisateur
     * 
     * @route user/edit/roles/{id}
     * @name app_user_roles
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'utilisateur
     */
    #[Route('/user/roles/{id<\d+>}', name: 'app_user_roles')]
    public function setRoles(User $objUsers, Request $request, EntityManagerInterface $entityManager): Response
    {
       $roles = [];
       if($request->request->get('user-role-modal-'.$objUsers->getId().'-modo')){
            $roles[] ='ROLE_ORGA';
       }
       if($request->request->get('user-role-modal-'.$objUsers->getId().'-admin')){
            $roles[] ='ROLE_ADMIN';
       }
       $this->addFlash(
        'success',
        "Les modifications ont été enregistrées"
        );
        $objUsers->setRoles($roles);
        $entityManager->flush();
        return $this->redirectToRoute('app_user');
    }
     /**
     * Page  de profil d'un utilisateur
     * 
     * @route user/profil/roles/{id}
     * @name app_user_profil
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'utilisateur
     */
    #[Route('/profil/{id<\d+>}', name: 'app_profil')]
    public function profil(User $objUsers): Response
    {
        // Condition si le profil nest pas le meme que la session alors redirige
        if ($objUsers != $this->getUser()) {
            return $this->redirectToRoute('app_error');
        }
        return $this->render('user/profil.html.twig', [
            'users' => $objUsers,
        ]);
    }
     /**
     * Page  de modification de son profil
     * 
     * @route profil/edit/{id}
     * @name app_profil_edit
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     * @param Request $request (dépendance) Objet contenant la requête envoyé par le navigateur ($_POST/$_GET)
     *
     * @return Response Réponse HTTP renvoyée au navigateur avec le formulaire de modification de l'utilisateur
     */
    #[Route('/profil/edit/{id<\d+>}', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit_profil(Int $id,User $objUsers, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Condition si le profil nest pas le meme que la session alors redirige
        if ($objUsers != $this->getUser()) {
            return $this->redirectToRoute('app_error');
        }
        // Création du formulaire pour l'affichage
        // @param PlateformType : correspond à la classe du formulaire
        // @param $objPlateforms : l'objet qui remplit par défaut le formulaire et qui sera mis à jour
        $formUserEdit = $this->createForm(UserType::class, $objUsers);
        // On dit au formulaire de récupérer les données de la requête ($_POST)
        $formUserEdit->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        if($formUserEdit->isSubmitted() && $formUserEdit->isValid())
        {
            // Met à jour les données en base
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées"
            );
            return $this->redirectToRoute('app_user_profil', ['id' => $id]);
        }
        return $this->render('user/edit.html.twig', [
            'users'         => $objUsers,
            'formEdit'      => $formUserEdit
        ]);
    }
    /**
     * Route de suppression de son profil
     * 
     * @route profil/delete/{id}
     * @name app_profil_delete
     * 
     * @param User $objUsers Entité User correspondante à l'ID transmise dans l'URL
     * @param EntityManagerInterface $entityManager (dépendance) Gestionnaire d'entités
     *
     * @return Response Réponse HTTP renvoyée au navigateur
     */
    #[Route('/profil/delete/{id<\d+>}', name: 'app_profil_delete')]
    public function delete_profil(User $objUsers, EntityManagerInterface $entityManager): Response
    {
        try {
            // Prépare l'objet à la suppression
            $entityManager->remove($objUsers);
            // On lance la suppression en base
            $entityManager->flush();
            // Si tout s'est bien passé, je redirige vers la liste
            $this->addFlash(
                'success',
                "La suppression a été effectuée"
            );
            return $this->redirectToRoute('app_login');
        }
        catch(\Exception $exc) {
            // Flash qui s'affichera à l'écran avec le message d'erreur de l'exception
            $this->addFlash(
                'error',
                $exc->getMessage()
            );            
            // Je redirige vers la page principale de plateform
            return $this->redirectToRoute('app_login');
        }
    }
}
