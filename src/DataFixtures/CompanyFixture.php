<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends Fixture
{
	public function load(ObjectManager $manager): void {
		$companies = ['Яндекс', 'Google', 'Детский Мир', 'Пятерочка', 'Вкусно и точка'];

		foreach ($companies as $company) {
			$newCompany = new Company();
			$newCompany->setName($company);
			$manager->persist($newCompany);
		}

		$manager->flush();
	}
}
