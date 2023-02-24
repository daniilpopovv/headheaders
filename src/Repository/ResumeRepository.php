<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Resume;
use App\Entity\User;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resume::class);
    }

    public function save(Resume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Resume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkInvite(?User $user, Resume $resume)
    {
        $qb = $this->createQueryBuilder('resume');

        $qb->where('resume.id = ' . $resume->getId());
        $qb->join('resume.invites', 'vacancy', Join::WITH, 'vacancy.owner = :ownerId');
        $qb->setParameter('ownerId', $user->getId());

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function checkReply(?User $user, Resume $resume)
    {
        $qb = $this->createQueryBuilder('resume');

        $qb->where('resume.id = ' . $resume->getId());
        $qb->join('resume.replies', 'vacancy', Join::WITH, 'vacancy.owner = :ownerId');
        $qb->setParameter('ownerId', $user->getId());

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function searchByOwner(?User $user): array
    {
        return $this->findBy(['owner' => $user]);
    }
}
