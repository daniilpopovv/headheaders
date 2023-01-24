<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recruiter;
use App\Entity\Seeker;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/register')]
class RegistrationController extends AbstractController
{
	#[Route('/{slug}', name: 'app_register')]
	public function registerSeeker(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager, string $slug): Response {
		$user = $slug === 'seeker' ? new Seeker() : new Recruiter();

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('password')->getData()
				)
			);

			$entityManager->persist($user);
			$entityManager->flush();

			return $userAuthenticator->authenticateUser(
				$user,
				$authenticator,
				$request
			);
		}

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}
