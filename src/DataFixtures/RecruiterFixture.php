<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecruiterFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CompanyFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $companyRepository = $manager->getRepository(Company::class);
        $companies = $companyRepository->findAll();

        $recruiters = [
            1 => [
                'username' => 'Nnandan',
                'fullName' => 'Кудрявцева Полина Николаевна',
                'email' => 'srour@mac.com',
            ],
            2 => [
                'username' => 'Dahli',
                'fullName' => 'Морозов Евгений Дмитриевич',
                'email' => 'lamprecht@aol.com',
            ],
            3 => [
                'username' => 'Nahamm',
                'fullName' => 'Пастухов Лев Русланович',
                'email' => 'garyjb@me.com',
            ],
            4 => [
                'username' => 'Gournell',
                'fullName' => 'Колесов Никита Ильич',
                'email' => 'mosses@sbcglobal.net',
            ],
            5 => [
                'username' => 'Pomona',
                'fullName' => 'Потапов Арсений Владиславович',
                'email' => 'world@verizon.net',
            ],
            6 => [
                'username' => 'Toniam',
                'fullName' => 'Алексеев Степан Глебович',
                'email' => 'petersen@comcast.net',
            ],
            7 => [
                'username' => 'Dreane',
                'fullName' => 'Лебедев Константин Саввич',
                'email' => 'murty@me.com',
            ],
            8 => [
                'username' => 'Jame',
                'fullName' => 'Гончарова Есения Никитична',
                'email' => 'josem@sbcglobal.net',
            ],
            9 => [
                'username' => 'Zubere',
                'fullName' => 'Сидорова Амина Константиновна',
                'email' => 'jshearer@mac.com',
            ],
            10 => [
                'username' => 'Welc',
                'fullName' => 'Трифонов Артём Глебович',
                'email' => 'carcus@att.net',
            ],
        ];

        foreach ($recruiters as $recruiter) {
            $newRecruiter = new User();
            $newRecruiter->setUsername($recruiter['username']);
            $newRecruiter->setFullName($recruiter['fullName']);
            $newRecruiter->setEmail($recruiter['email']);
            $newRecruiter->setCompany($companies[rand(0, 4)]);
            $newRecruiter->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
            $newRecruiter->setRoles([RoleEnum::user->value, RoleEnum::recruiter->value]);
            $manager->persist($newRecruiter);
        }

        $manager->flush();
    }
}
