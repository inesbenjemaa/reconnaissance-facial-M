<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessControlController extends AbstractController
{
    #[Route('/admin/authorization', name: 'app_authorization')]
    public function manageAuthorizations(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les utilisateurs
        $users = $entityManager->getRepository(User::class)->findAll();

        if ($request->isMethod('POST')) {
            // Récupérer les statuts des utilisateurs du formulaire
            $data = $request->request->all('access_status'); // Correct usage: 'access_status' is your form field

            // Mettre à jour les statuts d'accès pour chaque utilisateur
            foreach ($data as $userId => $status) {
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($user) {
                    $user->setAuthorizationStatus($status); // Assure-toi que la méthode est bien setAuthorizationStatus
                    $entityManager->persist($user);
                }
            }

            // Sauvegarder dans la base de données
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Les statuts d\'accès ont été mis à jour avec succès.');

            // Rediriger vers la page de gestion des autorisations
            return $this->redirectToRoute('app_authorization');
        }

        return $this->render('access_control/index.html.twig', [
            'users' => $users,
        ]);
    }
}
