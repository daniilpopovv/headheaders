<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vacancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vacancy>
 *
 * @method Vacancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacancy[]    findAll()
 * @method Vacancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function save(Vacancy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vacancy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByQuery(array $queryText, $querySkills)
    {
        $qb = $this->createQueryBuilder('vacancy');

        foreach ($queryText as $queryTextElement) {
            $qb->andWhere("vacancy.specialization LIKE '%$queryTextElement%'");
            $qb->orWhere("vacancy.description LIKE '%$queryTextElement%'");
        }

        foreach ($querySkills as $querySkill) {
            $qb->join('vacancy.skills', "vacancy_skill" . $querySkill, Join::WITH, "vacancy_skill" . $querySkill . ".id = '$querySkill'");
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
