<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SkillFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $skills = ['PHP', 'ООП', 'MySQL', 'PostgreSQL', 'Redis', 'VueJS', 'Bootstrap', 'Symfony', 'Git', 'TypeScript'];

        foreach ($skills as $skill) {
            $newSkill = new Skill();
            $newSkill->setName($skill);
            $manager->persist($newSkill);
        }

        $manager->flush();
    }
}
