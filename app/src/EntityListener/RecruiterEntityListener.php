<?php

namespace App\EntityListener;

use App\Entity\Recruiter;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Recruiter::class)]
class RecruiterEntityListener
{
    public function prePersist(Recruiter $recruiter): void
    {
        $recruiter->setRoles(['ROLE_USER', 'ROLE_RECRUITER']);
    }

}