<?php

namespace App\Controller\Auth;

use App\Entity\Account;
use App\Entity\User;
use App\Form\UserForgotPasswordType;
use App\Form\RegisterUserType;
use App\Form\ResetUserPasswordType;
use App\Manager\UserManager;
use DateTime;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
     public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
     {
         $user = new User();
         $form = $this->createForm(RegisterUserType::class, $user);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $existingEmail = $entityManager->getRepository(User::class)->findOneBy([
                 'email' => $user->getEmail(),
             ]);

             if ($existingEmail) {
                 $this->addFlash('error', 'Le E-mail est déjà utilisé.');
                 return $this->redirectToRoute('app_register');
             }

             $plainPassword = $form->get('password')->getData();

             $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

             $user->setPassword($hashedPassword);
             $userCount = $entityManager->getRepository(User::class)->count([]);

            if ($userCount === 0) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $selectedRole = $form->get('roles')->getData();
                $user->setRoles([$selectedRole]); 
            }

             $entityManager->persist($user);

             $entityManager->flush();
             $this->addFlash('success', 'Inscription réussie');
             return $this->redirectToRoute('app_login');
         }

         return $this->render('security/inscription.html.twig', [
             'form' => $form->createView(),
         ]);
     }

     #[Route(path: '/forgot-password', name: 'app_forgot_password')]
      public function forgotPassword(Request $request, ManagerRegistry $doctrine, UserManager $userManager): Response
      {
          $form = $this->createForm(UserForgotPasswordType::class);
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              $email = $form->getData();
              $checkEmail = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
              if (!$checkEmail) {
                  $this->addFlash('error', 'E-mail que vous avez renseignée n`est liée à aucun compte. Veuillez le vérifier à nouveau.');
                  return $this->redirectToRoute("app_forgot_password");
              }
              try {
                  $userManager->mailForgotPasswordAdmin($checkEmail);
                  $this->addFlash('success', 'E-mail a été envoyé pour réinitialiser votre mot de passe.');
              } catch (Exception $exception) {
                dd($exception);
                  $this->addFlash('error', 'Une erreur s`est produite. Merci d`essayer plus tard.');
              }
              unset($form);
              $form = $this->createForm(UserForgotPasswordType::class);
          }

          return $this->render("security/forgot-password.html.twig", [
                  'forgotPassword_form' => $form->createView()]
          );
      }

      #[Route(path: '/reset-password/{token}', name: 'app_reset_password')]
      public function resetPassword(Request $request, string $token, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): Response
      {
          $form = $this->createForm(ResetUserPasswordType::class);
          $form->handleRequest($request);
          $em = $doctrine->getManager();
          $user = $doctrine->getRepository(User::class)->findOneBy(['resetToken' => $token]);
          if (!$user) {
              $this->addFlash('error', 'Désolé, votre jeton de connexion n`est pas valide ou ce lien a déjà été utilisé. Veuillez en demander un autre.');
              return $this->redirectToRoute("app_forgot_password");
          }
          $timeStamp = $user->getTimestamp();
          $dateNow = new DateTime();
          $expiredAt = $timeStamp->modify("+10 minutes");
          if ($dateNow > $expiredAt) {
              $this->addFlash('error', 'Désolé, ce lien a expiré. Veuillez en demander un autre.');
              return $this->redirectToRoute("app_forgot_password");
          }
          if ($form->isSubmitted() && $form->isValid()) {
              $user->setResetToken(null);
              $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
              $em->persist($user);
              $em->flush();
              $this->addFlash('success', 'Le mot de passe a été modifié avec succès. Vous pouvez maintenant vous connecter à votre compte.');
              return $this->redirectToRoute("app_login");
          }

          return $this->render("security/reset-password.html.twig", [
                  'token' => $token,
                  'reset_user_password' => $form->createView()]
          );
      }
}
