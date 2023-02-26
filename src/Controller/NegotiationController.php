<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\RoleEnum;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NegotiationController extends AbstractController
{
    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/sent-invites', name: 'sent-invites')]
    public function sentInvites(VacancyRepository $vacancyRepository): Response
    {
        $vacancies = $vacancyRepository->findByOwner($this->getUser());
        foreach ($vacancies as $vacancy) {
            $invites = $vacancy->getInvites();
            foreach ($invites as $invite) {
                $objects[] = $invite;
            }
        }

        return $this->render('negotiation/negotiation.html.twig', [
            'title' => 'negotiation.invite.title.asRecruiter',
            'objects' => $objects ?? [],
            'vacancies' => $vacancyRepository->findByOwner($this->getUser()),
            'path_link' => 'viewResume',
        ]);
    }

    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/received-replies', name: 'received-replies')]
    public function receivedReplies(VacancyRepository $vacancyRepository): Response
    {
        $vacancies = $vacancyRepository->findByOwner($this->getUser());
        foreach ($vacancies as $vacancy) {
            $invites = $vacancy->getReplies();
            foreach ($invites as $reply) {
                $objects[] = $reply;
            }
        }

        return $this->render('negotiation/negotiation.html.twig', [
            'title' => 'negotiation.reply.title.asRecruiter',
            'objects' => $objects ?? [],
            'vacancies' => $vacancyRepository->findByOwner($this->getUser()),
            'path_link' => 'viewResume',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/sent-replies', name: 'sent-replies')]
    public function sentReplies(ResumeRepository $resumeRepository): Response
    {
        $resumes = $resumeRepository->findByOwner($this->getUser());
        foreach ($resumes as $resume) {
            $replies = $resume->getReplies();
            foreach ($replies as $reply) {
                $objects[] = $reply;
            }
        }

        return $this->render('negotiation/negotiation.html.twig', [
            'title' => 'negotiation.reply.title.asSeeker',
            'objects' => $objects ?? [],
            'resumes' => $resumeRepository->findByOwner($this->getUser()),
            'path_link' => 'viewVacancy',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/received-invites', name: 'received-invites')]
    public function receivedInvites(ResumeRepository $resumeRepository): Response
    {
        $resumes = $resumeRepository->findByOwner($this->getUser());
        foreach ($resumes as $resume) {
            $replies = $resume->getInvites();
            foreach ($replies as $invite) {
                $objects[] = $invite;
            }
        }

        return $this->render('negotiation/negotiation.html.twig', [
            'title' => 'negotiation.invite.title.asSeeker',
            'objects' => $objects ?? [],
            'resumes' => $resumeRepository->findByOwner($this->getUser()),
            'path_link' => 'viewVacancy',
        ]);
    }
}
