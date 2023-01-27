<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NegotiationController extends AbstractController
{
	#[Route('/sent-invites', name: 'sent-invites')]
	public function sentInvites(VacancyRepository $vacancyRepository): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вы пригласили на вакансии:',
			'vacancies' => $vacancyRepository->findBy(['recruiter' => $this->getUser()]),

			// TODO: '1-1) Не ясна цель использования, так как по сути одно и то же'
			// 'vacancies' => $vacancyRepository->findByRecruiter($this->getUser()),
		]);
	}

	#[Route('/received-replies', name: 'received-replies')]
	public function receivedReplies(VacancyRepository $vacancyRepository): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'На ваши вакансии откликнулись:',
			'vacancies' => $vacancyRepository->findBy(['recruiter' => $this->getUser()]),
		]);
	}

	#[Route('/sent-replies', name: 'sent-replies')]
	public function sentReplies(ResumeRepository $resumeRepository): Response {

		return $this->render('replies/index.html.twig', [
			'title' => 'Вы откликнулись на вакансии:',
			'resumes' => $resumeRepository->findBy(['seeker' => $this->getUser()]),
		]);
	}

	#[Route('/received-invites', name: 'received-invites')]
	public function receivedInvites(ResumeRepository $resumeRepository): Response {

		return $this->render('invites/index.html.twig', [
			'title' => 'Вас пригласили на вакансии:',
			'resumes' => $resumeRepository->findBy(['seeker' => $this->getUser()]),
		]);
	}
}
