<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitesController extends AbstractController
{
    #[Route('/invites', name: 'invites')]
    public function index(): Response
    {
        return $this->render('invites/index.html.twig');
    }
}
