<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class ObjectsSearchService
{
    public function search(
        EntityRepository $repository,
        string           $queryText = '',
        array            $querySkills = [],
        bool             $withAdditionalSkills = false
    ): array {
        $qb = $repository->createQueryBuilder('o');

        if ($queryText) {
            $this->searchByText(qb: $qb, queryText: $queryText);
        }

        if ($querySkills) {
            $this->searchPartialInterception(qb: $qb, querySkills: $querySkills);
        }

        $relevantObjects['partial'] = $qb->getQuery()->getResult();
        $relevantObjects['full'] = $this->extractFullInterception(
            partialRelevantObjects: $relevantObjects['partial'],
            querySkills: $querySkills,
            withAdditionalSkills: $withAdditionalSkills
        );

        return $relevantObjects;
    }

    public function searchByText(QueryBuilder $qb, string $queryText): QueryBuilder
    {
        $queryTextWords = preg_split('/[\s,-]+/', $queryText);
        foreach ($queryTextWords as $key => $queryTextWord) {
            $qb
                ->orWhere($qb->expr()->like(
                    $qb->expr()->lower('o.specialization'),
                    $qb->expr()->lower(':queryTextWord' . $key)
                ))
                ->orWhere($qb->expr()->like(
                    $qb->expr()->lower('o.description'),
                    $qb->expr()->lower(':queryTextWord' . $key)
                ))
                ->setParameter('queryTextWord' . $key, "%$queryTextWord%", Types::STRING);
        }

        return $qb;
    }

    public function searchPartialInterception(QueryBuilder $qb, array $querySkills): QueryBuilder
    {
        $qb
            ->join('o.skills', 'skill', Join::WITH, $qb->expr()->in(
                'skill',
                ':querySkills'
            ))
            ->setParameter('querySkills', $querySkills);

        return $qb;
    }

    public function extractFullInterception(
        array $partialRelevantObjects,
        array $querySkills,
        bool  $withAdditionalSkills
    ): array {
        $fullRelevantObjects = [];

        foreach ($partialRelevantObjects as $partialRelevantObject) {
            $relevantObjectSkills = $partialRelevantObject->getSkills()->map(fn($skill) => $skill->getId())->toArray();

            if (!array_diff($querySkills, $relevantObjectSkills) &&
                ($withAdditionalSkills || count($relevantObjectSkills) === count($querySkills))
            ) {
                $fullRelevantObjects[] = $partialRelevantObject;
            }
        }

        return $fullRelevantObjects;
    }
}
