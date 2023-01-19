<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsesController extends AbstractController
{
    #[Route('/responses', name: 'responses')]
    public function index(ResumeRepository $resumeRepository, VacancyRepository $vacancyRepository): Response
    {
        $userRoles = $this->getUser()->getRoles();

        if (in_array('ROLE_SEEKER', $userRoles)) {
            $role = 'seeker';
            $title = 'Вы откликнулись на вакансии:';

            $resumes = $resumeRepository->findBy(['seeker' => $this->getUser()]);
        } elseif (in_array('ROLE_RECRUITER', $userRoles)) {
            $role = 'recruiter';
            $title = 'На ваши вакансии откликнулись:';

            $vacancies = $vacancyRepository->findBy(['recruiter' => $this->getUser()]);
        }

        return $this->render('responses/index.html.twig', [
            'title' => $title ?? 'Отклики',
            'role' => $role ?? 'Гость',
            'resumes' => $resumes ?? [],
            'vacancies' => $vacancies ?? [],
        ]);
    }
}
