<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@recipehub.com');
        $admin->setPseudo('admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Cuisinier
        $cuisinier = new User();
        $cuisinier->setEmail('chef@recipehub.com');
        $cuisinier->setPseudo('chef');
        $cuisinier->setPassword($this->passwordHasher->hashPassword($cuisinier, 'chef123'));
        $cuisinier->setRoles(['ROLE_CUISINIER']);
        $manager->persist($cuisinier);
        $this->addReference('user_cuisinier', $cuisinier);

        // 5 utilisateurs normaux
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPseudo($faker->userName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}