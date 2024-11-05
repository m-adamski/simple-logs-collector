<?php

namespace App\Helper;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityHelper {
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly TokenStorageInterface       $tokenStorage,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function getUser(): ?User {
        if (null !== ($storageUser = $this->tokenStorage->getToken()->getUser())) {
            return $storageUser instanceof User ? $storageUser : null;
        }

        return null;
    }

    public function upgradePassword(User $user, bool $flush = false): void {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            throw new \UnexpectedValueException("Value of the 'plainPassword' variable cannot be null");
        }

        $password = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($password);

        if ($flush) {
            $this->userRepository->upgradePassword($user, $password);
        }
    }

    public function saveUser(User $user): void {
        $this->userRepository->persist($user, true);
    }

    public function isAuthenticated(): bool {
        return $this->getUser() instanceof User;
    }
}
