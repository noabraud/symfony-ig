<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheapsharkController extends AbstractController
{
    #[Route('/', name: 'app_cheapshark')]
    public function index(): Response
    {
        return $this->render('cheapshark/index.html.twig', [
            'controller_name' => 'CheapsharkController',
        ]);
    }
}
