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
use App\Service\ObjectsSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vacancies')]
class VacancyController extends AbstractController
{
    #[Route('/', name: 'vacancies')]
    public function index(
        Request              $request,
        VacancyRepository    $vacancyRepository,
        ObjectsSearchService $objectsSearchService
    ): Response {
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        $vacancies = match (true) {
            $searchForm->isSubmitted() && $searchForm->isValid() =>
            $objectsSearchService->search(
                $vacancyRepository,
                $searchForm->get('query_text')->getViewData(),
                $searchForm->get('query_skills')->getViewData(),
                true
            )['full'],
            default => $vacancyRepository->findAll()
        };

        return $this->render('object/object_list.html.twig', [
            'objects' => $vacancies,
            'search_form' => $searchForm->createView(),
            'path_link' => 'viewVacancy',
            'title' => 'vacancy.title.page.all',
        ]);
    }

    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/create', name: 'createVacancy')]
    public function createVacancy(Request $request, VacancyRepository $vacancyRepository): Response
    {
        $vacancy = new Vacancy();
        $form = $this->createForm(VacancyFormType::class, $vacancy);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setOwner($this->getUser());
            $vacancyRepository->save($vacancy, true);

            return $this->redirectToRoute('viewVacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('object/object_actions.html.twig', [
            'action_form' => $form->createView(),
            'title' => 'vacancy.title.page.create',
        ]);
    }

    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/edit/{id}', name: 'editVacancy', requirements: ['id' => '^\d+$'])]
    public function editVacancy(Vacancy $vacancy, Request $request, VacancyRepository $vacancyRepository): Response
    {
        if ($vacancy->getOwner() !== $this->getUser()) {
            return $this->redirectToRoute('myVacancies');
        }

        $form = $this->createForm(VacancyFormType::class, $vacancy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setOwner($this->getUser());
            $vacancyRepository->save($vacancy, true);

            return $this->redirectToRoute('viewVacancy', ['id' => $vacancy->getId()]);
        }

        return $this->render('object/object_actions.html.twig', [
            'action_form' => $form->createView(),
            'title' => 'vacancy.title.page.edit',
        ]);
    }

    #[IsGranted(RoleEnum::recruiter->value)]
    #[Route('/my', name: 'myVacancies')]
    public function myVacancies(VacancyRepository $vacancyRepository): Response
    {
        return $this->render('object/object_list.html.twig', [
            'objects' => $vacancyRepository->findByOwner($this->getUser()),
            'path_link' => 'viewVacancy',
            'title' => 'vacancy.title.page.my',
        ]);
    }

    #[Route('/{id}', name: 'viewVacancy', requirements: ['id' => '^\d+$'])]
    public function viewVacancy(
        Vacancy                       $vacancy,
        Request                       $request,
        ObjectsSearchService          $objectsSearchService,
        VacancyRepository             $vacancyRepository,
        ResumeRepository              $resumeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if ($authorizationChecker->isGranted(RoleEnum::seeker->value)) {
            $form = $this->createForm(VacancyReplyType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && $form->get('replies')->getData() !== []) {
                $vacancy->addReply($form->get('replies')->getData()->first());
                $vacancyRepository->save($vacancy, true);

                return $this->redirectToRoute('viewVacancy', ['id' => $vacancy->getId()]);
            }
        }

        $relevantResumes = $objectsSearchService->search(
            repository: $resumeRepository,
            querySkills: $vacancy->getSkills()->map(fn($skill) => $skill->getId())->toArray()
        );

        return $this->render('object/object_view.html.twig', [
            'object' => $vacancy,
            'object_type' => 'vacancy',
            'vacancy_form' => isset($form) ? $form->createView() : null,
            'relevant_full' => $relevantResumes['full'],
            'relevant_partial' => $relevantResumes['partial'],
            'is_invite' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ?
                $vacancyRepository->checkInvite($this->getUser(), $vacancy) : false,
            'is_reply' => $authorizationChecker->isGranted('IS_AUTHENTICATED') ?
                $vacancyRepository->checkReply($this->getUser(), $vacancy) : false,
            'path_link' => 'viewResume',
            'edit_link' => 'editVacancy'
        ]);
    }
}
