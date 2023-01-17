<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeFormType;
use App\Form\ResumeInviteType;
use App\Form\SearchFormType;
use App\Repository\ResumeRepository;
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
    public function index(ResumeRepository $resumeRepository, Request $request): Response
    {
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            if ($searchForm->get('query_text')->getData()) {
                $queryText = preg_split('/[,-.\s;]+/', $searchForm->get('query_text')->getData());
                $querySkills = $searchForm->get('query_skills')->getNormData();
                $resumesFromTextQuery = $resumeRepository->searchByQuery($queryText);

                foreach ($resumesFromTextQuery as $resumeFromTextQuery) {
                    if (!array_diff($querySkills->toArray(), $resumeFromTextQuery->getSkills()->toArray())) {
                        $resumes[] = $resumeFromTextQuery;
                    }
                }
            } else {
                $querySkills = $searchForm->get('query_skills')->getNormData();

                foreach ($resumeRepository->findAll() as $resume) {
                    if (!array_diff($querySkills->toArray(), $resume->getSkills()->toArray())) {
                        $resumes[] = $resume;
                    }
                }
            }

            return $this->render('resume/index.html.twig', [
                'resumes' => $resumes ?? [],
                'search_form' => $searchForm,
            ]);
        }

        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findAll(),
            'search_form' => $searchForm,
        ]);
    }

    #[Route('/create', name: 'create_resume')]
    public function createResume(Request $request): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeFormType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resume->setSeeker($this->getUser());

            $this->entityManager->persist($resume);
            $this->entityManager->flush();

            return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
        }

        return $this->render('resume/createResume.html.twig', [
            'resume_form' => $form->createView(),
            'title' => 'Создание резюме',
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_resume')]
    public function editResume(Resume $resume, Request $request, ResumeRepository $resumeRepository): Response
    {
        $seeker = $this->getUser();

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
            'title' => 'Редактирование резюме',
        ]);
    }

    #[Route('/my', name: 'my_resumes')]
    public function myResumes(ResumeRepository $resumeRepository): Response
    {
        $seeker = $this->getUser();

        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findBy(['seeker' => $seeker]),
            'my_resumes' => true,
        ]);
    }

    #[Route('/{id}', name: 'view_resume')]
    public function viewResume(Resume $resume, Request $request, VacancyRepository $vacancyRepository): Response
    {
        $userRoles = $this->getUser() !== null ? $this->getUser()->getRoles() : null;

        if ($this->getUser()) {
            if (in_array('ROLE_SEEKER', $userRoles)) {
                $role = 'seeker';

                if ($resume->getSeeker() === $this->getUser()) {
                    foreach ($vacancyRepository->findAll() as $vacancy) {
                        if (!in_array($resume, $vacancy->getResponses()->toArray())) {
                            if (!array_diff($vacancy->getSkills()->toArray(), $resume->getSkills()->toArray())) {
                                $relevant_vacancies[] = $vacancy;
                            }
                        }
                    }
                }
            } elseif (in_array('ROLE_RECRUITER', $userRoles)) {
                $role = 'recruiter';
                $vacancies = $vacancyRepository->findBy([
                    'recruiter' => $this->getUser(),
                ]);

                $isInvite = (bool)array_intersect($vacancies, $resume->getInvites()->toArray());
                $isResponse = (bool)array_intersect($vacancies, $resume->getRespondedVacancies()->toArray());

                $form = $this->createForm(ResumeInviteType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid() && $form->get('invites')->getViewData() !== []) {
                    $form_view_data = $form->get('invites')->getViewData();
                    $vacancy = $vacancyRepository->findOneBy(['id' => $form_view_data[0]]);
                    $resume->addInvite($vacancy);

                    $this->entityManager->persist($resume);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
                }
            }
        }

        return $this->render('resume/viewResume.html.twig', [
            'resume' => $resume,
            'resume_form' => isset($form) ? $form->createView() : null,
            'role' => $role ?? 'guest',
            'relevant_vacancies' => $relevant_vacancies ?? [],
            'isInvite' => $isInvite ?? false,
            'isResponse' => $isResponse ?? false,
        ]);
    }
}