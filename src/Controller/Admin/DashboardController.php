<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Resume;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\Vacancy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(ResumeCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Headheaders')
            ->setTranslationDomain('dashboard');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToRoute('dashboard.links.homepage', 'fas fa-home', 'homepage'),
            MenuItem::linkToCrud('dashboard.links.resume', 'fas fa-file', Resume::class),
            MenuItem::linkToCrud('dashboard.links.vacancy', 'fas fa-briefcase', Vacancy::class),
            MenuItem::linkToCrud('dashboard.links.skill', 'fas fa-lightbulb', Skill::class),
            MenuItem::linkToCrud('dashboard.links.user', 'fas fa-magnifying-glass', User::class),
            MenuItem::linkToCrud('dashboard.links.company', 'fas fa-building', Company::class),
        ];
    }
}
