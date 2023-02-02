<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminFixture extends Fixture
{
	public function load(ObjectManager $manager): void {
		$admin = new User();
		$admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
		$admin->setUsername('admin');
		$admin->setFullName('Админ Админович');
		$admin->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
		$admin->setEmail('admin@localhost.ru');
		$manager->persist($admin);
		$manager->flush();
	}

}
