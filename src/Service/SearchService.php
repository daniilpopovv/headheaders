<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class SearchService
{
	/**
	 * @throws Exception
	 */
	public function search(Request $request, EntityRepository $repository) {
		if ($repository instanceof ResumeRepository or $repository instanceof VacancyRepository) {
			$queryText = preg_split('/[,-.\s;]+/', $request->get('search_form')['query_text']) ?? '';
			$querySkills = $request->get('search_form')['query_skills'] ?? [];

			$qb = $repository->createQueryBuilder('o');

			foreach ($queryText as $queryTextElement) {
				$qb->orWhere("o.specialization LIKE '%$queryTextElement%'");
				$qb->orWhere("o.description LIKE '%$queryTextElement%'");
			}

			foreach ($querySkills as $querySkill) {
				$id = $querySkill;
				$qb->join('o.skills', "skill" . $id, Join::WITH, "skill" . $id . ".id = '$id'");
			}
		} else {
			throw new Exception('Invalid repository type');
		}

		return $qb
			->getQuery()
			->getResult();
	}

	/**
	 * @throws Exception
	 */
	public function searchBySkills(Collection $querySkills, EntityRepository $repository) {
		if ($repository instanceof ResumeRepository or $repository instanceof VacancyRepository) {
			$qb = $repository->createQueryBuilder('o');

			foreach ($querySkills as $querySkill) {
				$id = $querySkill->getId();
				$qb->join('vacancy.skills', "skill" . $id, Join::WITH, "skill" . $id . ".id = '$id'");
			}
		} else {
			throw new Exception('Invalid repository type');
		}

		return $qb
			->getQuery()
			->getResult();
	}

	/**
	 * @throws Exception
	 */
	public function searchByOwner(?User $user, EntityRepository $repository) {
		if ($repository instanceof ResumeRepository or $repository instanceof VacancyRepository) {
			$result = $repository->findBy(['owner' => $user]);
		} else {
			throw new Exception('Invalid repository type');
		}

		return $result;
	}
}
