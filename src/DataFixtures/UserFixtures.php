<?php

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('amdin');

        $user->setPassword(base64_encode('admin'));

        $user->setEmail('no-reply@auth.com');

        $manager->persist($user);
        $manager->flush();
    }
}
