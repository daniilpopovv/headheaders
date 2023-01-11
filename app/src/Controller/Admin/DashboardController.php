<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Recruiter;
use App\Entity\Resume;
use App\Entity\Seeker;
use App\Entity\Skill;
use App\Entity\Vacancy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(ResumeCrudController::class)->generateUrl();

        return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Headheaders');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Обратно на сайт', 'fas fa-home', 'homepage');
        yield MenuItem::linkToCrud('Резюме', 'fas fa-file', Resume::class);
        yield MenuItem::linkToCrud('Вакансии', 'fas fa-briefcase', Vacancy::class);
        yield MenuItem::linkToCrud('Скиллы', 'fas fa-lightbulb', Skill::class);
        yield MenuItem::linkToCrud('Искатели', 'fas fa-magnifying-glass', Seeker::class);
        yield MenuItem::linkToCrud('Хантеры', 'fas fa-eye', Recruiter::class);
        yield MenuItem::linkToCrud('Компании', 'fas fa-building', Company::class);
    }
}
