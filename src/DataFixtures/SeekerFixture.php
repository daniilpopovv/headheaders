<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SeekerFixture extends Fixture
{
	public function load(ObjectManager $manager): void {
		$seekers = [
			1 => [
				'username' => 'Onathiar',
				'fullName' => 'Аникина Алиса Максимовна',
				'email' => 'madanm@aol.com',
			],
			2 => [
				'username' => 'Ferynara',
				'fullName' => 'Иванов Тимофей Богданович',
				'email' => 'conteb@icloud.com',
			],
			3 => [
				'username' => 'Jenise',
				'fullName' => 'Никитина Виктория Константиновна',
				'email' => 'sekiya@att.net',
			],
			4 => [
				'username' => 'Doner',
				'fullName' => 'Крылова Алиса Глебовна',
				'email' => 'gfody@me.com',
			],
			5 => [
				'username' => 'Dencin',
				'fullName' => 'Грачев Артём Павлович',
				'email' => 'mirod@att.net',
			],
			6 => [
				'username' => 'Merv',
				'fullName' => 'Михайлов Артём Артёмович',
				'email' => 'fwitness@icloud.com',
			],
			7 => [
				'username' => 'Endir',
				'fullName' => 'Мельников Илья Артёмович',
				'email' => 'ccohen@msn.com',
			],
			8 => [
				'username' => 'Chus',
				'fullName' => 'Попова Дарья Тимофеевна',
				'email' => 'dsugal@aol.com',
			],
			9 => [
				'username' => 'Pablen',
				'fullName' => 'Иванов Егор Егорович',
				'email' => 'sacraver@verizon.net',
			],
			10 => [
				'username' => 'Damm',
				'fullName' => 'Горшкова Милана Матвеевна',
				'email' => 'breegster@verizon.net',
			],
		];

		foreach ($seekers as $seeker) {
			$newSeeker = new User();
			$newSeeker->setUsername($seeker['username']);
			$newSeeker->setFullName($seeker['fullName']);
			$newSeeker->setEmail($seeker['email']);
			$newSeeker->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
			$newSeeker->setRoles(['ROLE_USER', 'ROLE_SEEKER']);
			$manager->persist($newSeeker);
		}

		$manager->flush();
	}
}
