<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recruiter;
use App\Entity\Seeker;
use App\Form\RegistrationFormType;
use App\Repository\RecruiterRepository;
use App\Repository\SeekerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class RegistrationController extends AbstractController
{
	public function FormAction($form, $user, $repository, $userPasswordHasher): ?Response {
		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('password')->getData()
				)
			);

			$repository->save($user, true);

			return $this->redirectToRoute('app_login');
		}

		return null;
	}

	#[Route('/seeker', name: 'app_register_seeker')]
	public function registerSeeker(Request $request, SeekerRepository $seekerRepository, UserPasswordHasherInterface $userPasswordHasher): Response {
		$user = new Seeker();

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		$this->FormAction($form, $user, $seekerRepository, $userPasswordHasher);

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}

	#[Route('/recruiter', name: 'app_register_recruiter')]
	public function registerRecruiter(Request $request, RecruiterRepository $recruiterRepository, UserPasswordHasherInterface $userPasswordHasher): Response {
		$user = new Recruiter();

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		$this->FormAction($form, $user, $recruiterRepository, $userPasswordHasher);

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}
