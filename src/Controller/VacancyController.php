<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vacancy;
use App\Form\SearchFormType;
use App\Form\VacancyFormType;
use App\Form\VacancyReplyType;
use App\Repository\ResumeRepository;
use App\Repository\SeekerRepository;
use App\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vacancies')]
class VacancyController extends AbstractController
{
	public function __construct(private readonly EntityManagerInterface $entityManager) {
	}

	#[Route('/', name: 'vacancies')]
	public function index(VacancyRepository $vacancyRepository, Request $request): Response {
		$searchForm = $this->createForm(SearchFormType::class);
		$searchForm->handleRequest($request);
		if ($searchForm->isSubmitted() && $searchForm->isValid()) {
			$queryText = preg_split('/[,-.\s;]+/', $searchForm->get('query_text')->getViewData()) ?? '';
			$querySkills = $searchForm->get('query_skills')->getNormData();
			$vacancies = $vacancyRepository->searchByQuery($queryText, $querySkills);

			return $this->render('vacancy/index.html.twig', [
				'vacancies' => $vacancies ?? [],
				'search_form' => $searchForm,
			]);
		}

		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $vacancyRepository->findAll(),
			'search_form' => $searchForm,
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/create', name: 'create_vacancy')]
	public function createVacancy(Request $request): Response {
		$vacancy = new Vacancy();
		$form = $this->createForm(VacancyFormType::class, $vacancy);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$vacancy->setRecruiter($this->getUser());

			$this->entityManager->persist($vacancy);
			$this->entityManager->flush();

			return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
		}

		return $this->render('vacancy/createVacancy.html.twig', [
			'vacancy_form' => $form->createView(),
			'title' => 'Создание вакансии',
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/edit/{id}', name: 'edit_vacancy')]
	public function editVacancy(Vacancy $vacancy, Request $request, VacancyRepository $vacancyRepository): Response {
		$recruiter = $this->getUser();

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
			'title' => 'Редактирование вакансии',
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/my', name: 'my_vacancies')]
	public function myVacancies(VacancyRepository $vacancyRepository): Response {
		$recruiter = $this->getUser();

		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $vacancyRepository->findBy(['recruiter' => $recruiter]),
			'my_vacancies' => true,
		]);
	}

	#[Route('/{id}', name: 'view_vacancy')]
	public function viewVacancy(Vacancy $vacancy, Request $request, ResumeRepository $resumeRepository, SeekerRepository $seekerRepository): Response {
		$userRoles = $this->getUser() !== null ? $this->getUser()->getRoles() : null;

		if ($userRoles) {
			switch (true) {
				case in_array('ROLE_RECRUITER', $userRoles):
					$role = 'recruiter';
					if ($vacancy->getRecruiter() === $this->getUser()) {
						$relevant_resumes = $resumeRepository->searchByQuery([], $vacancy->getSkills());
					}
					break;
				case in_array('ROLE_SEEKER', $userRoles):
					$role = 'seeker';
					$seeker = $seekerRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

					$resumes = $resumeRepository->findBy([
						'seeker' => $seeker,
					]);

					$isInvite = (bool)array_intersect($resumes, $vacancy->getInvitedResumes()->toArray());
					$isReply = in_array($seeker, $vacancy->getWhoReplied()->toArray());


					$form = $this->createForm(VacancyReplyType::class);
					$form->handleRequest($request);
					if ($form->isSubmitted() && $form->isValid() && $form->get('replies')->getViewData() !== []) {
						$form_view_data = $form->get('replies')->getViewData();
						$resume = $resumeRepository->findOneBy(['id' => $form_view_data[0]]);
						$vacancy->addReply($resume);
						$vacancy->addWhoReplied($seeker);

						$this->entityManager->persist($vacancy);
						$this->entityManager->flush();

						return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
					}
					break;
			}
		}

		return $this->render('vacancy/viewVacancy.html.twig', [
			'vacancy' => $vacancy,
			'vacancy_form' => isset($form) ? $form->createView() : null,
			'role' => $role ?? 'guest',
			'relevant_resumes' => $relevant_resumes ?? [],
			'isInvite' => $isInvite ?? false,
			'isReply' => $isReply ?? false,
		]);
	}
}
