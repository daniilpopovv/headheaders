<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NegotiationController extends AbstractController
{
	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/sent-invites', name: 'sent-invites')]
	public function sentInvites(VacancyRepository $vacancyRepository): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вы пригласили на вакансии:',
			'vacancies' => $vacancyRepository->findByOwner($this->getUser()),
		]);
	}

	#[IsGranted('ROLE_RECRUITER')]
	#[Route('/received-replies', name: 'received-replies')]
	public function receivedReplies(VacancyRepository $vacancyRepository): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'На ваши вакансии откликнулись:',
			'vacancies' => $vacancyRepository->findByOwner($this->getUser()),
		]);
	}

	#[IsGranted('ROLE_SEEKER')]
	#[Route('/sent-replies', name: 'sent-replies')]
	public function sentReplies(ResumeRepository $resumeRepository): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'Вы откликнулись на вакансии:',
			'resumes' => $resumeRepository->findByOwner($this->getUser()),
		]);
	}

	#[IsGranted('ROLE_SEEKER')]
	#[Route('/received-invites', name: 'received-invites')]
	public function receivedInvites(ResumeRepository $resumeRepository): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вас пригласили на вакансии:',
			'resumes' => $resumeRepository->findByOwner($this->getUser()),
		]);
	}
}
