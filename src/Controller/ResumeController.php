<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Resume;
use App\Enum\RoleEnum;
use App\Form\ResumeFormType;
use App\Form\ResumeInviteType;
use App\Form\SearchFormType;
use App\Repository\ResumeRepository;
use App\Repository\VacancyRepository;
use App\Service\ObjectsSearchService;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function index(
        Request              $request,
        ResumeRepository     $resumeRepository,
        ObjectsSearchService $objectsSearchService
    ): Response {
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        $resumes = match (true) {
            $searchForm->isSubmitted() && $searchForm->isValid() =>
            $objectsSearchService->search(
                $resumeRepository,
                $searchForm->get('query_text')->getViewData(),
                $searchForm->get('query_skills')->getViewData(),
                true
            )['full'],
            default => $resumeRepository->findAll()
        };

        return $this->render('object/object_list.html.twig', [
            'objects' => $resumes,
            'search_form' => $searchForm,
            'path_link' => 'viewResume',
            'title' => 'resume.title.page.all',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/create', name: 'createResume')]
    public function createResume(Request $request, ResumeRepository $resumeRepository): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeFormType::class, $resume);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resume->setOwner($this->getUser());
            $resumeRepository->save($resume, true);

            return $this->redirectToRoute('viewResume', ['id' => $resume->getId()]);
        }

        return $this->render('object/object_actions.html.twig', [
            'action_form' => $form->createView(),
            'title' => 'resume.title.page.create',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/edit/{id}', name: 'editResume', requirements: ['id' => '^\d+$'])]
    public function editResume(Resume $resume, Request $request, ResumeRepository $resumeRepository): Response
    {
        if ($resume->getOwner() !== $this->getUser()) {
            return $this->redirectToRoute('myResumes');
        }

        $form = $this->createForm(ResumeFormType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resume->setOwner($this->getUser());
            $resumeRepository->save($resume, true);

            return $this->redirectToRoute('viewResume', ['id' => $resume->getId()]);
        }

        return $this->render('object/object_actions.html.twig', [
            'action_form' => $form->createView(),
            'title' => 'resume.title.page.edit',
        ]);
    }

    #[IsGranted(RoleEnum::seeker->value)]
    #[Route('/my', name: 'myResumes')]
    public function myResumes(ResumeRepository $resumeRepository): Response
    {
        return $this->render('object/object_list.html.twig', [
            'objects' => $resumeRepository->findByOwner($this->getUser()),
            'path_link' => 'viewResume',
            'title' => 'resume.title.page.my',
        ]);
    }

    #[Route('/{id}', name: 'viewResume', requirements: ['id' => '^\d+$'])]
    public function viewResume(
        Resume                        $resume,
        Request                       $request,
        ObjectsSearchService          $objectsSearchService,
        VacancyRepository             $vacancyRepository,
        ResumeRepository              $resumeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if ($authorizationChecker->isGranted(RoleEnum::recruiter->value)) {
            $form = $this->createForm(ResumeInviteType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() && $form->get('invites')->getData() !== []) {
                $resume->addInvite($form->get('invites')->getData()->first());
                $resumeRepository->save($resume, true);

                return $this->redirectToRoute('viewResume', ['id' => $resume->getId()]);
            }
        }

        $relevantVacancies = $objectsSearchService->search(
            repository: $vacancyRepository,
            querySkills: $resume->getSkills()->map(fn($skill) => $skill->getId())->toArray()
        );

        return $this->render('object/object_view.html.twig', [
            'object' => $resume,
            'object_type' => 'resume',
            'resume_form' => isset($form) ? $form->createView() : null,
            'relevant_full' => $relevantVacancies['full'],
            'relevant_partial' => $relevantVacancies['partial'],
            'is_invite' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ?
                $resumeRepository->checkInvite($this->getUser(), $resume) : false,
            'is_reply' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ?
                $resumeRepository->checkReply($this->getUser(), $resume) : false,
            'path_link' => 'viewVacancy',
            'edit_link' => 'editResume',
        ]);
    }
}
