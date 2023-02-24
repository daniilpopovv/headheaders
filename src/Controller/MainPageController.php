<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('homepage', ['_locale' => 'ru']);
    }

    #[Route('/{_locale<%app.supported_locales%>}', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }
}
