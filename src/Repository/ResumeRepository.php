<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Resume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resume>
 *
 * @method Resume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resume[]    findAll()
 * @method Resume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResumeRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Resume::class);
	}

	public function save(Resume $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Resume $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function searchByQuery(array $queryText, $querySkills) {
		$qb = $this->createQueryBuilder('resume');

		foreach ($queryText as $queryTextElement) {
			$qb->orWhere("resume.specialization LIKE '%$queryTextElement%'");
			$qb->orWhere("resume.description LIKE '%$queryTextElement%'");
		}

		foreach ($querySkills as $querySkill) {
			$id = $querySkill->getId();
			$qb->join('resume.skills', "resume_skill" . $id, Join::WITH, "resume_skill" . $id . ".id = '$id'");
		}

		return $qb
			->getQuery()
			->getResult();
	}
}
