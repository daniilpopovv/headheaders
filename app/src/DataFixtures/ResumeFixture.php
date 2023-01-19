<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Resume;
use App\Entity\Seeker;
use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ResumeFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SeekerFixture::class,
            SkillFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $seekerRepository = $manager->getRepository(Seeker::class);
        $seekers = $seekerRepository->findAll();
        $skillRepository = $manager->getRepository(Skill::class);
        $skills = $skillRepository->findAll();

        $specializations = ['Senior PHP разработчик', 'Senior developer TypeScript', 'Golang разработчик',
            'Java разработчик', 'Программист-разработчик 1С', 'Веб-разработчик',
            'Программист-стажер Desktop приложений', 'Младший Golang разработчик', 'PHP-программист Junior', 'Младший PHP-программист'];

        foreach ($specializations as $specialization) {
            $newResume = new Resume();
            $newResume->setSpecialization($specialization);
            $newResume->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $newResume->setSalary(rand(13890, 500000));
            foreach ($skills as $skill) {
                if (rand(0, 1)) {
                    $newResume->addSkill($skill);
                }
            }
            $newResume->setSeeker($seekers[rand(0, 9)]);
            $manager->persist($newResume);
        }

        $manager->flush();
    }
}
