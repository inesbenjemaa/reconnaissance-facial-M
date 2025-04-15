<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{

    #[Route('/home', name: 'home')]
public function home(Request $request): Response
{
    // Si l'utilisateur n'est pas connecté
    if (!$this->getUser()) {
        return $this->redirectToRoute('app_login');
    }

    // Si l'utilisateur n'a pas encore vérifié son visage
    if (!$request->getSession()->get('face_verified', false)) {
        return $this->redirectToRoute('face_check');
    }

    // Sinon, accès au home (dashboard)
    return $this->render('home.html.twig'); // ou dashboard.html.twig
}


#[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
{
    // ✅ Si l'utilisateur est déjà connecté
    if ($this->getUser()) {
        // ➡️ Redirection selon le statut de vérification faciale
        if (!$request->getSession()->get('face_verified', false)) {
            return $this->redirectToRoute('face_check');
        }
        return $this->redirectToRoute('home');
    }

    // ❌ Utilisateur non connecté, on affiche le formulaire
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}





    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode est interceptée par le firewall.');
    }

    #[Route('/face-check', name: 'face_check')]
    public function faceCheck(Request $request): Response
    {
        // ❌ Si utilisateur non connecté, on le renvoie vers login
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // ✅ Si déjà vérifié, on évite cette page
        if ($request->getSession()->get('face_verified', false)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('security/face_check.html.twig');
    }

    #[Route('/face-verify', name: 'face_verify', methods: ['POST'])]
    public function verifyFace(Request $request): Response
    {
        $imageData = $request->request->get('face_image');
        if (!$imageData) {
            return new Response("Aucune image reçue", 400);
        }

        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = base64_decode($imageData);

        $imagePath = sys_get_temp_dir() . '/capture_' . uniqid() . '.png';
        file_put_contents($imagePath, $imageData);

        $pythonScriptPath = realpath(__DIR__ . '/../../face_recognition/verify.py');
        $escapedImagePath = escapeshellarg($imagePath);
        $escapedPythonScriptPath = escapeshellarg($pythonScriptPath);

        $command = "python3 $escapedPythonScriptPath $escapedImagePath";
        $status = trim(shell_exec($command));

       


        if (strtolower($status) === 'autorisé') {
            $request->getSession()->set('face_verified', true);
            return $this->redirectToRoute('home');
        }
        
        return new Response("Échec de la vérification faciale : $status", 403);
    }
}




