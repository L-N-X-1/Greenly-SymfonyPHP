<?php

namespace App\Manager;

use App\Entity\User;
use App\Service\MailSender;
use Doctrine\Persistence\ManagerRegistry;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class UserManager
{
    
    public function __construct( private UserPasswordHasherInterface $passwordHasher,private MailSender $mailSender, ManagerRegistry $doctrine, private RouterInterface $router, private TokenGeneratorInterface $tokenGenerator)
    {
        $this->mailSender = $mailSender;
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @return void
     */
    public function hashUserPassword(User $user): void
    {
        $generator = new ComputerPasswordGenerator();
        $generator->setLength(12)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true);
        $generatedPassword = $generator->generatePassword();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $generatedPassword
        );
        $user->setPassword($hashedPassword);
    }

    /**
     * @param User $user
     * @return void
     */
    public function resetHashUserPassword(User $user): void
    {
        $plaintextPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
    }

    /**
     * @param $checkEmail
     * @return bool
     * @throws UserNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function mailForgotPasswordAdmin($checkEmail): bool
    {
        if (!$checkEmail) {
            throw new AccessDeniedException('User not found.');
        }        

        $route = $this->router;
        $em = $this->doctrine->getManager();
        $tokenGenerator = $this->tokenGenerator;
        $token = $tokenGenerator->generateToken();
        $url = $route->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        $checkEmail->setResetToken($token);
        $em->persist($checkEmail);
        $em->flush();
        $container['data'] = $checkEmail;
        $container['view'] = "security/send.forgot-password.twig";
        $container['mailInfo'] = [
            'receiverList' => [
                'Email' => $checkEmail->getEmail(),
                'Name' => $checkEmail->getFullName(),
            ],
            'mailExpeditor' => 'hedi.laater@gmail.com',
            'nameExpeditor' => 'Omar',
            'subject' => 'Omar'
        ];

        return $this->mailSender->sendEmail($container, $url);
    }

   /**
    * Undocumented function
    *
    * @param [type] $user
    * @param [type] $plaintextPassword
    * @return boolean
    */
    public function createAccountAuth($user, $plaintextPassword): bool
    {

        $container['data'] = $user;
        $container['view'] = 'security/send.data-auth.twig';
        $container['mailInfo'] = [
            'receiverList' => [
                'Email' => $user->getEmail(),
                'Name' => $user->getFullName(),
            ],
            'mailExpeditor' => 'hedi.laater@gmail.com',
            'nameExpeditor' => 'Omar',
            'subject' => 'Omar'
        ];

        return $this->mailSender->sendEmail($container, $plaintextPassword);
    }
}
