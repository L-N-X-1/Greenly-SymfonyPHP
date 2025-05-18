<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\ResetUserPasswordType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findUsersWithoutAdminRole();

        return $this->render('admin/pages/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserManager $userManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $plainPassword = $form->get('password')->getData();
                $userManager->resetHashUserPassword($user);
                $userManager->createAccountAuth($user, $plainPassword);
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'User created successfully.');
    
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating the user. Please try again.');
            }
        }
    
        return $this->render('admin/pages/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/pages/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user)->remove('roles')->remove('password');
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imgFile = $form->get('imgFile')->getData();
    
                if ($imgFile) {
                    $user->setImgFile($imgFile);
                }
    
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'User updated successfully.');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while updating the user. Please try again.');
            }
        }
    
        return $this->render('admin/pages/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/delete/{id}', name: 'app_user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, $id): Response
    {
        try {
            $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
    
            if (!$user) {
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('app_user_index');
            }
    
            $entityManager->remove($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while deleting the user.');
        }
    
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/{id}/reset-password', name: 'app_user_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $form = $this->createForm(ResetUserPasswordType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'Password reset successfully.');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while resetting the password. Please try again.');
            }
        }
    
        return $this->render('admin/pages/user/reset-password.html.twig', [
            'user' => $user,
            'reset_user_password' => $form,
        ]);
    }
}
