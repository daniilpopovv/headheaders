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
    public function index(VacancyRepository $vacancyRepository, ResumeRepository $resumeRepository, SeekerRepository $seekerRepository): Response
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
            $userIdentifier = $this->getUser()->getUserIdentifier();
            $recruiter = $recruiterRepository->findOneBy(['username' => $userIdentifier]);
            $vacancy->setRecruiter($recruiter);

            $this->entityManager->persist($vacancy);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('vacancy/createVacancy.html.twig', [
            'vacancy_form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_vacancy')]
    public function editVacancy(Request $request, RecruiterRepository $recruiterRepository, VacancyRepository $vacancyRepository, int $id): Response
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $recruiter = $recruiterRepository->findOneBy(['username' => $userIdentifier]);
        $vacancy = $vacancyRepository->findOneBy(['id' => $id]);

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
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $recruiter = $recruiterRepository->findOneBy(['username' => $userIdentifier]);

        return $this->render('vacancy/index.html.twig', [
            'vacancies' => $vacancyRepository->findBy(['recruiter' => $recruiter]),
            'my_vacancies' => true,
        ]);
    }

    #[Route('/{id}', name: 'view_vacancy')]
    public function viewVacancy(Vacancy $vacancy, Request $request, SeekerRepository $seekerRepository, ResumeRepository $resumeRepository): Response
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $seeker = $seekerRepository->findOneBy(['username' => $userIdentifier]);
        $resumes = $resumeRepository->findBy(['seeker' => $seeker]);
        $isResponse = false;
        $isRecruiter = false;

        foreach ($this->getUser()->getRoles() as $role) {

            if ($role === 'ROLE_RECRUITER') {
                $isRecruiter = true;
                break;
            }
        }

        $relevant_resumes = [];

        if ($isRecruiter) {
            foreach ($resumeRepository->findAll() as $resume) {
                if (!(array_diff($vacancy->getSkills()->toArray(), $resume->getSkills()->toArray()))) {
                    array_push($relevant_resumes, $resume);
                }
            }
        }

        // TODO: Оптимизировать
        foreach ($resumes as $resume1) {
            foreach ($vacancy->getResponses() as $resume2) {
                if ($resume1 === $resume2) {
                    $isResponse = true;
                }
            }
        }

        $form = $this->createForm(VacancyResponseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_view_data = $form->get('responses')->getViewData();
            $idOfResume = $form_view_data[0];
            $resume = $resumeRepository->findOneBy(['id' => $idOfResume]);
            $vacancy->addResponse($resume);

            $this->entityManager->persist($vacancy);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('vacancy/viewVacancy.html.twig', [
            'vacancy' => $vacancy,
            'vacancy_form' => $form->createView(),
            'isResponse' => $isResponse,
            'relevant_resumes' => $relevant_resumes,
        ]);
    }
}
