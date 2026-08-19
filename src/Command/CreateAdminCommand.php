<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create-admin',
    description: 'Crée ou met à jour un administrateur.'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email de l’administrateur')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $this->resolveEmail($input, $io);
        if ($email === null) {
            return Command::FAILURE;
        }

        $plainPassword = $this->resolvePassword($input, $io);

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user === null) {
            if ($plainPassword === null || $plainPassword === '') {
                $io->error('Mot de passe requis pour créer un nouvel utilisateur.');
                return Command::FAILURE;
            }
            $user = new User();
            $user->setEmail($email);
        }

        $user->setRoles($this->ensureAdminRole($user->getRoles()));

        if ($plainPassword !== null && $plainPassword !== '') {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Administrateur prêt : %s', $email));
        return Command::SUCCESS;
    }

    private function resolveEmail(InputInterface $input, SymfonyStyle $io): ?string
    {
        $email = $input->getOption('email');
        if (is_string($email) && $email !== '') {
            return $this->validateEmail($email, $io);
        }

        $email = $io->ask('Email de l’administrateur', null, function (?string $value) use ($io) {
            return $this->validateEmail($value, $io);
        });

        return is_string($email) ? $email : null;
    }

    private function resolvePassword(InputInterface $input, SymfonyStyle $io): ?string
    {
        $password = $input->getOption('password');
        if (is_string($password)) {
            return $password === '' ? null : $password;
        }

        return $io->askHidden('Mot de passe (laisser vide pour ne pas modifier)', function (?string $value) {
            return $value ?? '';
        });
    }

    private function validateEmail(?string $value, SymfonyStyle $io): ?string
    {
        $email = $value ? trim($value) : null;
        if ($email === null || $email === '') {
            $io->error('Email requis.');
            return null;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Email invalide.');
            return null;
        }
        return $email;
    }

    /**
     * @param list<string> $roles
     * @return list<string>
     */
    private function ensureAdminRole(array $roles): array
    {
        if (!in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_values(array_unique($roles));
    }
}
