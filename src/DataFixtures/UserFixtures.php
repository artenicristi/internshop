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

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setUserName('admin_test1');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adMin1'));
        $admin->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setUserName('user_test1');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'userTest1'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);

        $manager->flush();
    }
}
