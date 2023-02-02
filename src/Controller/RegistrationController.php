<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class RegistrationController extends AbstractController
{
	#[Route('/{slug}', name: 'app_register', requirements: ['slug' => '^(seeker|recruiter)$'])]
	public function registerSeeker(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, string $slug): Response {
		$user = new User();

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('password')->getData()
				)
			);

			if ($slug === RoleEnum::seeker->name) {
				$user->setRoles([RoleEnum::user->value, RoleEnum::seeker->value]);
			}

			if ($slug === RoleEnum::recruiter->name) {
				$user->setRoles([RoleEnum::user->value, RoleEnum::recruiter->value]);
			}

			$userRepository->save($user, true);

			return $this->redirectToRoute('app_login');
		}

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}
