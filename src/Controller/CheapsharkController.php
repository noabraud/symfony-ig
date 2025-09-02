<?php

namespace App\Controller;

use App\Service\CheapsharkApi;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheapsharkController extends AbstractController
{
    #[Route('/', name: 'app_cheapshark')]
    public function index(CheapsharkApi $api): Response
    {
        $game = $api->getGame(612);

        return $this->render('cheapshark/index.html.twig', [
            'controller_name' => 'CheapsharkController',
            'title' => $game['info']['title'],
            'image' => $game['info']['thumb'],
            'cheapest' => $game['cheapestPriceEver']['price'],
            'deals' => $game['deals'],
        ]);
    }
}
