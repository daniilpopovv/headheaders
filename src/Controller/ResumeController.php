<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeFormType;
use App\Form\ResumeInviteType;
use App\Form\SearchFormType;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/resumes')]
class ResumeController extends AbstractController
{
	#[Route('/', name: 'resumes')]
	public function index(ResumeRepository $resumeRepository, Request $request): Response {
		$searchForm = $this->createForm(SearchFormType::class);
		$searchForm->handleRequest($request);
		if ($searchForm->isSubmitted() && $searchForm->isValid()) {
			$queryText = preg_split('/[,-.\s;]+/', $searchForm->get('query_text')->getViewData()) ?? '';
			$querySkills = $searchForm->get('query_skills')->getNormData();
			$resumes = $resumeRepository->searchByQuery($queryText, $querySkills);

			return $this->render('resume/index.html.twig', [
				'resumes' => $resumes ?? [],
				'search_form' => $searchForm->createView(),
			]);
		}

		return $this->render('resume/index.html.twig', [
			'resumes' => $resumeRepository->findAll(),
			'search_form' => $searchForm->createView(),
		]);
	}

	#[IsGranted('ROLE_SEEKER')]
	#[Route('/create', name: 'create_resume')]
	public function createResume(Request $request, ResumeRepository $resumeRepository): Response {
		$resume = new Resume();
		$form = $this->createForm(ResumeFormType::class, $resume);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$resume->setOwner($this->getUser());
			$resumeRepository->save($resume, true);

			return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Создание резюме',
		]);
	}

	#[IsGranted('ROLE_SEEKER')]
	#[Route('/edit/{id}', name: 'edit_resume', requirements: ['id' => '^\d+$'])]
	public function editResume(Resume $resume, Request $request, ResumeRepository $resumeRepository): Response {
		if ($resume->getOwner() !== $this->getUser()) {
			return $this->redirectToRoute('my_resumes');
		}

		$form = $this->createForm(ResumeFormType::class, $resume);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$resume->setOwner($this->getUser());
			$resumeRepository->save($resume, true);

			return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
		}

		return $this->render('propertyActions.html.twig', [
			'actions_form' => $form->createView(),
			'title' => 'Редактирование резюме',
		]);
	}

	#[IsGranted('ROLE_SEEKER')]
	#[Route('/my', name: 'my_resumes')]
	public function myResumes(ResumeRepository $resumeRepository): Response {
		return $this->render('resume/index.html.twig', [
			'resumes' => $resumeRepository->findByOwner($this->getUser()),
			'my_resumes' => true,
		]);
	}

	#[Route('/{id}', name: 'view_resume', requirements: ['id' => '^\d+$'])]
	public function viewResume(Resume $resume, Request $request, VacancyRepository $vacancyRepository, ResumeRepository $resumeRepository, AuthorizationCheckerInterface $authorizationChecker): Response {
		$relevant_vacancies = $vacancyRepository->searchByQuery([], $resume->getSkills());

		if ($authorizationChecker->isGranted('ROLE_RECRUITER')) {
			$form = $this->createForm(ResumeInviteType::class);
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid() && $form->get('invites')->getViewData() !== []) {
				$resume->addInvite($form->get('invites')->getNormData()[0]);
				$resumeRepository->save($resume, true);

				return $this->redirectToRoute('view_resume', ['id' => $resume->getId()]);
			}
		}

		return $this->render('resume/viewResume.html.twig', [
			'resume' => $resume,
			'resume_form' => isset($form) ? $form->createView() : null,
			'relevant_vacancies' => $relevant_vacancies ?? [],
			'isInvite' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ? $resumeRepository->checkInvite($this->getUser(), $resume) : false,
			'isReply' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ? $resumeRepository->checkReply($this->getUser(), $resume) : false,
		]);
	}
}
