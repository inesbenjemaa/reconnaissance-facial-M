<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users'=>$userRepository->findAll(),
        ]);
    }


    #[Route('/admin/user/{id}/editor', name: 'app_user_to_editor')]
    public function changeRole(EntityManagerInterface $entityManager, User $user): Response
{
    $user->setRoles(["ROLE_EDITOR", "ROLE_USER"]);
    $entityManager->flush();

    $this->addFlash(type: 'success', message: 'the editor role has been added to your user');

    return $this->redirectToRoute('app_user');
}


#[Route('/admin/{id}/remove/editor/role}', name: 'app_user_remove_editor_role')]
public function editorRoleRemove(EntityManagerInterface $entityManager, User $user): Response
{
$user->setRoles([]);
$entityManager->flush();

$this->addFlash(type: 'danger', message: 'the editor role has been removed to your user');

return $this->redirectToRoute('app_user');
}



#[Route('/admin/user/{id}/remove/', name: 'app_user_remove')]
public function userRemove(EntityManagerInterface $entityManager, $id, UserRepository $userRepository): Response
{
    $userFind = $userRepository->find($id);
    $entityManager->remove($userFind);
    $entityManager->flush();

    $this->addFlash(type: 'danger', message: 'your user has been deleted');

    return $this->redirectToRoute(route: 'app_user');
}













#[Route('/admin/user/add', name: 'app_user_add')]
public function addUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'User added successfully!');

        return $this->redirectToRoute('app_user');
    }

    return $this->render('user/form.html.twig', [
        'form' => $form->createView(),
        'title' => 'Add User',
    ]);
}












#[Route('/admin/user/{id}/edit', name: 'app_user_edit')]
public function editUser(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Re-hash password if it has been changed
        if ($form->get('password')->getData()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        $entityManager->flush();

        $this->addFlash('success', 'User updated successfully!');

        return $this->redirectToRoute('app_user');
    }

    return $this->render('user/form.html.twig', [
        'form' => $form->createView(),
        'title' => 'Edit User',
    ]);
}





}
