<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsesController extends AbstractController
{
    #[Route('/responses', name: 'responses')]
    public function index(): Response
    {
        return $this->render('responses/index.html.twig');
    }
}
