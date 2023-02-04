<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vacancy;
use App\Enum\RoleEnum;
use App\Form\SearchFormType;
use App\Form\VacancyFormType;
use App\Form\VacancyReplyType;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use App\Service\SearchService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vacancies')]
class VacancyController extends AbstractController
{
	/**
	 * @throws Exception
	 */
	#[Route('/', name: 'vacancies')]
	public function index(Request $request, VacancyRepository $vacancyRepository, SearchService $searchService): Response {
		$searchForm = $this->createForm(SearchFormType::class);
		$vacancies = $vacancyRepository->findAll();

		$searchForm->handleRequest($request);
		if ($searchForm->isSubmitted() && $searchForm->isValid()) {
			$vacancies = $searchService->search($request, $vacancyRepository);
		}

		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $vacancies,
			'search_form' => $searchForm->createView(),
		]);
	}

	#[IsGranted(RoleEnum::recruiter->value)]
	#[Route('/create', name: 'create_vacancy')]
	public function createVacancy(Request $request, VacancyRepository $vacancyRepository): Response {
		$vacancy = new Vacancy();
		$form = $this->createForm(VacancyFormType::class, $vacancy);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$vacancy->setOwner($this->getUser());
			$vacancyRepository->save($vacancy, true);

			return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Создание вакансии',
		]);
	}

	#[IsGranted(RoleEnum::recruiter->value)]
	#[Route('/edit/{id}', name: 'edit_vacancy', requirements: ['id' => '^\d+$'])]
	public function editVacancy(Vacancy $vacancy, Request $request, VacancyRepository $vacancyRepository): Response {
		if ($vacancy->getOwner() !== $this->getUser()) {
			return $this->redirectToRoute('my_vacancies');
		}

		$form = $this->createForm(VacancyFormType::class, $vacancy);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$vacancy->setOwner($this->getUser());
			$vacancyRepository->save($vacancy, true);

			return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Редактирование вакансии',
		]);
	}

	/**
	 * @throws Exception
	 */
	#[IsGranted(RoleEnum::recruiter->value)]
	#[Route('/my', name: 'my_vacancies')]
	public function myVacancies(VacancyRepository $vacancyRepository, SearchService $searchService): Response {
		return $this->render('vacancy/index.html.twig', [
			'vacancies' => $searchService->searchByOwner($this->getUser(), $vacancyRepository),
			'my_vacancies' => true,
		]);
	}

	/**
	 * @throws Exception
	 */
	#[Route('/{id}', name: 'view_vacancy', requirements: ['id' => '^\d+$'])]
	public function viewVacancy(Vacancy $vacancy, Request $request, SearchService $searchService, VacancyRepository $vacancyRepository, ResumeRepository $resumeRepository, AuthorizationCheckerInterface $authorizationChecker): Response {
		$relevant_resumes = $searchService->searchBySkills($vacancy->getSkills(), $resumeRepository);

		if ($authorizationChecker->isGranted(RoleEnum::seeker->value)) {
			$form = $this->createForm(VacancyReplyType::class);
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid() && $form->get('replies')->getViewData() !== []) {
				$vacancy->addReply($form->get('replies')->getNormData()[0]);
				$vacancyRepository->save($vacancy, true);

				return $this->redirectToRoute('view_vacancy', ['id' => $vacancy->getId()]);
			}
		}

		return $this->render('vacancy/viewVacancy.html.twig', [
			'vacancy' => $vacancy,
			'vacancy_form' => isset($form) ? $form->createView() : null,
			'relevant_resumes' => $relevant_resumes,
			'isInvite' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ? $vacancyRepository->checkInvite($this->getUser(), $vacancy) : false,
			'isReply' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ? $vacancyRepository->checkReply($this->getUser(), $vacancy) : false,
		]);
	}
}
