<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Seeker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Seeker>
 *
 * @method Seeker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seeker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seeker[]    findAll()
 * @method Seeker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeekerRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Seeker::class);
	}

	public function save(Seeker $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Seeker $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 */
	public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
		if (!$user instanceof Seeker) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
		}

		$user->setPassword($newHashedPassword);

		$this->save($user, true);
	}
}
