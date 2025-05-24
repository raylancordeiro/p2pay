<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $shopkeeper = new User();
        $shopkeeper->setName("Foo Bar");
        $shopkeeper->setCpf("14769770030");
        $shopkeeper->setEmail("foobar@gmail.com");
        $shopkeeper->setPassword("12345");
        $shopkeeper->setRole(UserRole::SHOPKEEPER);

        $person = new User();
        $person->setName("John Doe");
        $person->setCpf("45739320038");
        $person->setEmail("person@gmail.com");
        $person->setPassword("12345");
        $person->setRole(UserRole::PERSON);

        $manager->persist($person);
        $manager->persist($shopkeeper);
        $manager->flush();
    }
}
