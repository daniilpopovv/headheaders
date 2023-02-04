<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
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
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Vacancy::class);
	}

	public function save(Vacancy $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Vacancy $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function checkInvite(?User $user, Vacancy $vacancy) {
		$qb = $this->createQueryBuilder('vacancy');

		$qb->where('vacancy.id = ' . $vacancy->getId());
		$qb->join('vacancy.invites', 'resume', Join::WITH, 'resume.owner = :ownerId');
		$qb->setParameter('ownerId', $user->getId());

		return $qb
			->getQuery()
			->getResult();
	}

	public function checkReply(?User $user, Vacancy $vacancy) {
		$qb = $this->createQueryBuilder('vacancy');

		$qb->where('vacancy.id = ' . $vacancy->getId());
		$qb->join('vacancy.replies', 'resume', Join::WITH, 'resume.owner = :ownerId');
		$qb->setParameter('ownerId', $user->getId());

		return $qb
			->getQuery()
			->getResult();
	}
}
