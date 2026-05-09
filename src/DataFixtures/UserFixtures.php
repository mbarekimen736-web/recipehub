<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('mbarekimen736@gmail.com');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
