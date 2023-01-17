<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('admin');
        $admin->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
        $manager->persist($admin);
        $manager->flush();
    }

}
