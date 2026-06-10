<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-test-users',
    description: 'Creates test admin and normal users.',
)]
class CreateTestUsersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepo = $this->entityManager->getRepository(User::class);

        // Create Admin
        $adminEmail = 'admin@greenly.com';
        $admin = $userRepo->findOneBy(['email' => $adminEmail]);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail($adminEmail);
            $admin->setFullName('Admin Greenly');
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
            $admin->setRoles(['ROLE_ADMIN']);
            $this->entityManager->persist($admin);
            $output->writeln("Admin created: $adminEmail / admin123");
        } else {
            $output->writeln("Admin already exists: $adminEmail");
        }

        // Create Normal User
        $userEmail = 'user@greenly.com';
        $user = $userRepo->findOneBy(['email' => $userEmail]);
        if (!$user) {
            $user = new User();
            $user->setEmail($userEmail);
            $user->setFullName('User Greenly');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $output->writeln("User created: $userEmail / user123");
        } else {
            $output->writeln("User already exists: $userEmail");
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
