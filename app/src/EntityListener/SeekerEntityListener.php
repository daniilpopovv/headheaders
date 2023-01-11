<?php

namespace App\EntityListener;

use App\Entity\Seeker;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Seeker::class)]
class SeekerEntityListener
{
    public function prePersist(Seeker $seeker): void
    {
        $seeker->setRoles(['ROLE_USER', 'ROLE_SEEKER']);
    }

}