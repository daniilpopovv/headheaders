<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Resume;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\Vacancy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VacancyFixture extends Fixture implements DependentFixtureInterface
{
	public function getDependencies(): array {
		return [
			RecruiterFixture::class,
			SkillFixture::class,
			ResumeFixture::class,
		];
	}

	public function load(ObjectManager $manager): void {
		$userRepository = $manager->getRepository(User::class);
		$recruiters = $userRepository->findRecruiters();
		$skillRepository = $manager->getRepository(Skill::class);
		$skills = $skillRepository->findAll();
		$resumeRepository = $manager->getRepository(Resume::class);
		$resumes = $resumeRepository->findAll();

		$specializations = ['Senior PHP разработчик', 'Senior developer TypeScript', 'Golang разработчик',
			'Java разработчик', 'Программист-разработчик 1С', 'Веб-разработчик',
			'Программист-стажер Desktop приложений', 'Младший Golang разработчик', 'PHP-программист Junior', 'Младший PHP-программист'];

		foreach ($specializations as $specialization) {
			$newVacancy = new Vacancy();
			$newVacancy->setSpecialization($specialization);
			$newVacancy->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
			$newVacancy->setSalary(rand(13890, 500000));
			$newVacancy->setOwner($recruiters[rand(0, count($recruiters) - 1)]);
			foreach ($skills as $skill) if (rand(0, 1)) $newVacancy->addSkill($skill);
			foreach ($resumes as $resume) {
				if (rand(0, 1)) $newVacancy->addReply($resume);
				if (rand(0, 1)) $newVacancy->addInvite($resume);
			}
			$manager->persist($newVacancy);
		}

		$manager->flush();
	}
}
