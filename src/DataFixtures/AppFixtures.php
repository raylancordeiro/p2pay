<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Data fixture responsável por criar usuários padrão do sistema.
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $shopkeeper = new User();
        $shopkeeper->setName('Foo Bar');
        $shopkeeper->setCpf('14769770030');
        $shopkeeper->setEmail('foobar@gmail.com');
        $shopkeeper->setPassword('12345');
        $shopkeeper->setBalance(1000000);
        $shopkeeper->setRole(UserRole::SHOPKEEPER);

        $person = new User();
        $person->setName('John Doe');
        $person->setCpf('45739320038');
        $person->setEmail('person@gmail.com');
        $person->setPassword('12345');
        $person->setBalance(20000);
        $person->setRole(UserRole::PERSON);

        $manager->persist($person);
        $manager->persist($shopkeeper);
        $manager->flush();
    }
}
