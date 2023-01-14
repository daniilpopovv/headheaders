<?php

namespace App\Controller;

use App\Entity\Vacancy;
use App\Form\VacancyFormType;
use App\Form\VacancyResponseType;
use App\Repository\RecruiterRepository;
use App\Repository\ResumeRepository;
use App\Repository\SeekerRepository;
use App\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vacancies')]
class VacancyController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'vacancies')]
    public function index(VacancyRepository $vacancyRepository): Response
    {
        return $this->render('vacancy/index.html.twig', [
            'vacancies' => $vacancyRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'create_vacancy')]
    public function createVacancy(Request $request, RecruiterRepository $recruiterRepository): Response
    {
        $vacancy = new Vacancy();
        $form = $this->createForm(VacancyFormType::class, $vacancy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setRecruiter($recruiterRepository->findOneBy([
                'username' => $this->getUser()->getUserIdentifier()
            ]));

            $this->entityManager->persist($vacancy);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('vacancy/createVacancy.html.twig', [
            'vacancy_form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_vacancy')]
    public function editVacancy(Vacancy $vacancy, Request $request, RecruiterRepository $recruiterRepository, VacancyRepository $vacancyRepository): Response
    {
        $recruiter = $recruiterRepository->findOneBy([
            'username' => $this->getUser()->getUserIdentifier()
        ]);

        if (!($vacancy->getRecruiter() === $recruiter)) {
            return $this->redirectToRoute('my_vacancies', [
                'vacancies' => $vacancyRepository->findBy(['recruiter' => $recruiter]),
                'my_resumes' => true,
            ]);
        }

        $form = $this->createForm(VacancyFormType::class, $vacancy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setRecruiter($recruiter);

            $this->entityManager->persist($vacancy);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('vacancy/createVacancy.html.twig', [
            'vacancy_form' => $form->createView(),
        ]);
    }

    #[Route('/my', name: 'my_vacancies')]
    public function myVacancies(VacancyRepository $vacancyRepository, RecruiterRepository $recruiterRepository): Response
    {

        $recruiter = $recruiterRepository->findOneBy([
            'username' => $this->getUser()->getUserIdentifier()
        ]);

        return $this->render('vacancy/index.html.twig', [
            'vacancies' => $vacancyRepository->findBy(['recruiter' => $recruiter]),
            'my_vacancies' => true,
        ]);
    }

    #[Route('/{id}', name: 'view_vacancy')]
    public function viewVacancy(Vacancy $vacancy, Request $request, SeekerRepository $seekerRepository, ResumeRepository $resumeRepository): Response
    {
        $relevant_resumes = null;
        $userRoles = $this->getUser() !== null ? $this->getUser()->getRoles() : null;
        $userIdentifier = $this->getUser() !== null ? $this->getUser()->getUserIdentifier() : '';
        $isInvite = false;
        $isResponse = false;

        if ($this->getUser()) {
            if (in_array('ROLE_RECRUITER', $userRoles)) {
                $role = 'recruiter';

                foreach ($resumeRepository->findAll() as $resume) {
                    if (!in_array($vacancy, $resume->getInvites()->toArray())) {
                        if (!array_diff($vacancy->getSkills()->toArray(), $resume->getSkills()->toArray())) {
                            $relevant_resumes[] = $resume;
                        }
                    }
                }
            } elseif (in_array('ROLE_SEEKER', $userRoles)) {
                $role = 'seeker';
                $resumes = $resumeRepository->findBy([
                    'seeker' => $seekerRepository->findOneBy([
                        'username' => $userIdentifier,
                    ])
                ]);

                $isInvite = (bool)array_intersect($resumes, $vacancy->getInvitedResumes()->toArray());
                $isResponse = (bool)array_intersect($resumes, $vacancy->getResponses()->toArray());


                $form = $this->createForm(VacancyResponseType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $form_view_data = $form->get('responses')->getViewData();
                    $resume = $resumeRepository->findOneBy(['id' => $form_view_data[0]]);
                    $vacancy->addResponse($resume);

                    $this->entityManager->persist($vacancy);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
                }
            }
        }

        return $this->render('vacancy/viewVacancy.html.twig', [
            'vacancy' => $vacancy,
            'vacancy_form' => isset($form) ? $form->createView() : null,
            'role' => $role ?? 'guest',
            'relevant_resumes' => $relevant_resumes,
            'isInvite' => $isInvite,
            'isResponse' => $isResponse,
        ]);
    }
}
