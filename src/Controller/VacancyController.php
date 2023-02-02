<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vacancy;
use App\Form\SearchFormType;
use App\Form\VacancyFormType;
use App\Form\VacancyReplyType;
use App\Repository\ResumeRepository;
use App\Repository\UserRepository;
use App\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
				'search_form' => $searchForm->createView(),
			]);
		}

		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $vacancyRepository->findAll(),
			'search_form' => $searchForm->createView(),
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/create', name: 'create_vacancy')]
	public function createVacancy(Request $request): Response {
		$vacancy = new Vacancy();
		$form = $this->createForm(VacancyFormType::class, $vacancy);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$vacancy->setOwner($this->getUser());

			$this->entityManager->persist($vacancy);
			$this->entityManager->flush();

			return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Создание вакансии',
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/edit/{id}', name: 'edit_vacancy', requirements: ['id' => '^\d+$'])]
	public function editVacancy(Vacancy $vacancy, Request $request, VacancyRepository $vacancyRepository): Response {
		$recruiter = $this->getUser();

		if (!($vacancy->getOwner() === $recruiter)) {
			return $this->redirectToRoute('my_vacancies', [
				'vacancies' => $vacancyRepository->findBy(['owner' => $recruiter]),
				'my_resumes' => true,
			]);
		}

		$form = $this->createForm(VacancyFormType::class, $vacancy);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$vacancy->setOwner($recruiter);

			$this->entityManager->persist($vacancy);
			$this->entityManager->flush();

			return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Редактирование вакансии',
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/my', name: 'my_vacancies')]
	public function myVacancies(VacancyRepository $vacancyRepository): Response {
		$recruiter = $this->getUser();

		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $vacancyRepository->findBy(['owner' => $recruiter]),
			'my_vacancies' => true,
		]);
	}

	#[Route('/{id}', name: 'view_vacancy', requirements: ['id' => '^\d+$'])]
	public function viewVacancy(Vacancy $vacancy, Request $request, ResumeRepository $resumeRepository, UserRepository $userRepository, AuthorizationCheckerInterface $authorizationChecker): Response {
		$relevant_resumes = $resumeRepository->searchByQuery([], $vacancy->getSkills());

		if ($authorizationChecker->isGranted('ROLE_SEEKER')) {
			$seeker = $userRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

			$resumes = $resumeRepository->findBy([
				'seeker' => $this->getUser(),
			]);

			$isInvite = (bool)array_intersect($resumes, $vacancy->getInvitedResumes()->toArray());

			// TODO: изменить whoReplied на метод репозитория 1
			// $isReply = in_array($seeker, $vacancy->getWhoReplied()->toArray());


			$form = $this->createForm(VacancyReplyType::class);
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid() && $form->get('replies')->getViewData() !== []) {
				$form_view_data = $form->get('replies')->getViewData();
				$resume = $resumeRepository->findOneBy(['id' => $form_view_data[0]]);
				$vacancy->addReply($resume);

				// TODO: изменить whoReplied на метод репозитория 2
				// $vacancy->addWhoReplied($seeker);

				$this->entityManager->persist($vacancy);
				$this->entityManager->flush();

				return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
			}
		}

		return $this->render('vacancy/viewVacancy.html.twig', [
			'vacancy' => $vacancy,
			'vacancy_form' => isset($form) ? $form->createView() : null,
			'relevant_resumes' => $relevant_resumes,
			'isInvite' => $isInvite ?? false,
			'isReply' => $isReply ?? false,
		]);
	}
}
