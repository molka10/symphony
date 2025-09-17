<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WelcomecontrollerController extends AbstractController
{
    #[Route('/welcomecontroller', name: 'app_welcomecontroller')]
    public function index(): Response
    {
        return $this->render('welcomecontroller/index.html.twig', [
            'controller_name' => 'WelcomecontrollerController',
        ]);
    }
}
