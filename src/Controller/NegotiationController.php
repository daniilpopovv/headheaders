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
    #[Route('/{_locale<%app.supported_locales%>}/sent-invites', name: 'sent-invites')]
    public function sentInvites(VacancyRepository $vacancyRepository): Response
    {
        return $this->render('invites/index.html.twig', [
            'title' => 'Вы пригласили на вакансии:',
            'vacancies' => $vacancyRepository->searchByOwner($this->getUser()),
            'path_link' => 'viewResume',
        ]);
    }

    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/{_locale<%app.supported_locales%>}/received-replies', name: 'received-replies')]
    public function receivedReplies(VacancyRepository $vacancyRepository): Response
    {
        return $this->render('replies/index.html.twig', [
            'title' => 'На ваши вакансии откликнулись:',
            'vacancies' => $vacancyRepository->searchByOwner($this->getUser()),
            'path_link' => 'viewResume',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/{_locale<%app.supported_locales%>}/sent-replies', name: 'sent-replies')]
    public function sentReplies(ResumeRepository $resumeRepository): Response
    {
        return $this->render('replies/index.html.twig', [
            'title' => 'Вы откликнулись на вакансии:',
            'resumes' => $resumeRepository->searchByOwner($this->getUser()),
            'path_link' => 'viewVacancy',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/{_locale<%app.supported_locales%>}/received-invites', name: 'received-invites')]
    public function receivedInvites(ResumeRepository $resumeRepository): Response
    {
        return $this->render('invites/index.html.twig', [
            'title' => 'Вас пригласили на вакансии:',
            'resumes' => $resumeRepository->searchByOwner($this->getUser()),
            'path_link' => 'viewVacancy',
        ]);
    }
}
