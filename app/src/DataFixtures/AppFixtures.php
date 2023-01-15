<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Company;
use App\Entity\Recruiter;
use App\Entity\Resume;
use App\Entity\Seeker;
use App\Entity\Skill;
use App\Entity\Vacancy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $skills = ['PHP', 'ООП', 'MySQL', 'PostgreSQL', 'Redis', 'VueJS', 'Bootstrap', 'Symfony', 'Git', 'TypeScript'];
        $companies = ['Яндекс', 'Google', 'Детский Мир', 'Пятерочка', 'Вкусно и точка '];
        $specializations = ['Senior PHP разработчик', 'Senior developer TypeScript', 'Golang разработчик',
            'Java разработчик', 'Программист-разработчик 1С', 'Веб-разработчик',
            'Программист-стажер Desktop приложений', 'Младший Golang разработчик', 'PHP-программист Junior', 'Младший PHP-программист'];
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

        $admin = new Admin();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('admin');
        $admin->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
        $manager->persist($admin);

        foreach ($skills as $skill) {
            $newSkill = new Skill();
            $newSkill->setName($skill);
            $preparedSkills[] = $newSkill;
            $manager->persist($newSkill);
        }

        foreach ($companies as $company) {
            $newCompany = new Company();
            $newCompany->setName($company);
            $preparedCompanies[] = $newCompany;
            $manager->persist($newCompany);
        }

        foreach ($recruiters as $recruiter) {
            $newRecruiter = new Recruiter();
            $newRecruiter->setUsername($recruiter['username']);
            $newRecruiter->setFullName($recruiter['fullName']);
            $newRecruiter->setEmail($recruiter['email']);
            $newRecruiter->setCompany($preparedCompanies[rand(0, 4)]);
            $newRecruiter->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
            $preparedRecruiters[] = $newRecruiter;
            $manager->persist($newRecruiter);
        }

        foreach ($seekers as $seeker) {
            $newSeeker = new Seeker();
            $newSeeker->setUsername($seeker['username']);
            $newSeeker->setFullName($seeker['fullName']);
            $newSeeker->setEmail($seeker['email']);
            $newSeeker->setPassword('$2y$13$FT/5YkEB/UfBkb158b3Pqeg4QtCtESVzKMGsrRVaIVuXaZWgK4y4W');
            $preparedSeekers[] = $newSeeker;
            $manager->persist($newSeeker);
        }

        foreach ($specializations as $specialization) {
            $newVacancy = new Vacancy();
            $newVacancy->setSpecialization($specialization);
            $newVacancy->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut 
            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
            ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit 
            in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $newVacancy->setSalary(rand(13890, 500000));
            foreach ($preparedSkills as $preparedSkill) {
                if(rand(0,1)) {
                    $newVacancy->addSkill($preparedSkill);
                }
            }
            $newVacancy->setRecruiter($preparedRecruiters[rand(0, 9)]);
            $manager->persist($newVacancy);
        }

        foreach ($specializations as $specialization) {
            $newResume = new Resume();
            $newResume->setSpecialization($specialization);
            $newResume->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut 
            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
            ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit 
            in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            $newResume->setSalary(rand(13890, 500000));
            foreach ($preparedSkills as $preparedSkill) {
                if(rand(0,1)) {
                    $newResume->addSkill($preparedSkill);
                }
            }
            $newResume->setSeeker($preparedSeekers[rand(0,9)]);
            $manager->persist($newResume);
        }

        $manager->flush();
    }
}
