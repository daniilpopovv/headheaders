<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeFormType;
use App\Form\ResumeInviteType;
use App\Repository\RecruiterRepository;
use App\Repository\ResumeRepository;
use App\Repository\SeekerRepository;
use App\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resumes')]
class ResumeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'resumes')]
    public function index(ResumeRepository $resumeRepository): Response
    {
        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'create_resume')]
    public function createResume(Request $request, SeekerRepository $seekerRepository): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeFormType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userIdentifier = $this->getUser()->getUserIdentifier();
            $seeker = $seekerRepository->findOneBy(['username' => $userIdentifier]);
            $resume->setSeeker($seeker);

            $this->entityManager->persist($resume);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
        }

        return $this->render('resume/createResume.html.twig', [
            'resume_form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_resume')]
    public function editResume(Request $request, SeekerRepository $seekerRepository, ResumeRepository $resumeRepository, int $id): Response
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $seeker = $seekerRepository->findOneBy(['username' => $userIdentifier]);
        $resume = $resumeRepository->findOneBy(['id' => $id]);

        if (!($resume->getSeeker() === $seeker)) {
            return $this->redirectToRoute('my_resumes', [
                'resumes' => $resumeRepository->findBy(['seeker' => $seeker]),
                'my_resumes' => true,
            ]);
        }

        $form = $this->createForm(ResumeFormType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resume->setSeeker($seeker);

            $this->entityManager->persist($resume);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
        }

        return $this->render('resume/createResume.html.twig', [
            'resume_form' => $form->createView(),
        ]);
    }

    #[Route('/my', name: 'my_resumes')]
    public function myResumes(ResumeRepository $resumeRepository, SeekerRepository $seekerRepository): Response
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $seeker = $seekerRepository->findOneBy(['username' => $userIdentifier]);

        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findBy(['seeker' => $seeker]),
            'my_resumes' => true,
        ]);
    }

    #[Route('/{id}', name: 'view_resume')]
    public function viewResume(Resume $resume, Request $request, RecruiterRepository $recruiterRepository, VacancyRepository $vacancyRepository): Response
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $recruiter = $recruiterRepository->findOneBy(['username' => $userIdentifier]);
        $vacancies = $vacancyRepository->findBy(['recruiter' => $recruiter]);
        $isInvite = false;
        $isSeeker = false;

        foreach ($this->getUser()->getRoles() as $role) {

            if ($role === 'ROLE_SEEKER') {
                $isSeeker = true;
                break;
            }
        }

        $relevant_vacancies = [];

        if ($isSeeker) {
            foreach ($vacancyRepository->findAll() as $vacancy) {
                if (!(array_diff($vacancy->getSkills()->toArray(), $resume->getSkills()->toArray()))) {
                    array_push($relevant_vacancies, $vacancy);
                }
            }
        }

        // TODO: Оптимизировать
        foreach ($vacancies as $vacancy1) {
            foreach ($resume->getInvites() as $vacancy2) {
                if ($vacancy1 === $vacancy2) {
                    $isInvite = true;
                }
            }
        }

        $form = $this->createForm(ResumeInviteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_view_data = $form->get('invites')->getViewData();
            $idOfVacancy = $form_view_data[0];
            $vacancy = $vacancyRepository->findOneBy(['id' => $idOfVacancy]);
            $resume->addInvite($vacancy);

            $this->entityManager->persist($resume);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
        }

        return $this->render('resume/viewResume.html.twig', [
            'resume' => $resume,
            'resume_form' => $form->createView(),
            'isInvite' => $isInvite,
            'relevant_vacancies' => $relevant_vacancies,
        ]);
    }
}
