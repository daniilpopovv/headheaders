<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\RoleEnum;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use App\Service\SearchService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NegotiationController extends AbstractController
{
	/**
	 * @throws Exception
	 */
	#[IsGranted(RoleEnum::recruiter->value)]
	#[Route('/sent-invites', name: 'sent-invites')]
	public function sentInvites(VacancyRepository $vacancyRepository, SearchService $searchService): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вы пригласили на вакансии:',
			'vacancies' => $searchService->searchByOwner($this->getUser(), $vacancyRepository),
		]);
	}

	/**
	 * @throws Exception
	 */
	#[IsGranted(RoleEnum::recruiter->value)]
	#[Route('/received-replies', name: 'received-replies')]
	public function receivedReplies(VacancyRepository $vacancyRepository, SearchService $searchService): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'На ваши вакансии откликнулись:',
			'vacancies' => $searchService->searchByOwner($this->getUser(), $vacancyRepository),
		]);
	}

	/**
	 * @throws Exception
	 */
	#[IsGranted(RoleEnum::seeker->value)]
	#[Route('/sent-replies', name: 'sent-replies')]
	public function sentReplies(ResumeRepository $resumeRepository, SearchService $searchService): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'Вы откликнулись на вакансии:',
			'resumes' => $searchService->searchByOwner($this->getUser(), $resumeRepository),
		]);
	}

	/**
	 * @throws Exception
	 */
	#[IsGranted(RoleEnum::seeker->value)]
	#[Route('/received-invites', name: 'received-invites')]
	public function receivedInvites(ResumeRepository $resumeRepository, SearchService $searchService): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вас пригласили на вакансии:',
			'resumes' => $searchService->searchByOwner($this->getUser(), $resumeRepository),
		]);
	}
}
