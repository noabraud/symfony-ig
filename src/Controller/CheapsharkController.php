<?php

namespace App\Controller;

use App\Service\CheapsharkApi;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheapsharkController extends AbstractController
{
    #[Route('/', name: 'app_cheapshark_index')]
    public function index(CheapsharkApi $api): Response
    {
        $games = $api->getDeals();

        return $this->render('cheapshark/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/{id}', name: 'app_cheapshark', methods: ['GET'])]
    public function game(CheapsharkApi $api, int $id): Response
    {
        $game = $api->getGameLookup($id);

        return $this->render('cheapshark/gamePage.html.twig', [
            'title' => $game['info']['title'],
            'image' => $game['info']['thumb'],
            'cheapest' => $game['cheapestPriceEver']['price'],
            'deals' => $game['deals'],
        ]);
    }

    #[Route('/search', name: 'app_cheapshark_search', methods: ['GET'])]
    public function search(Request $request, CheapsharkApi $api): Response
    {
        $title = $request->query->get('title');

        if ($title) {
            $games = $api->getGamesList($title);
        }

        return $this->render('cheapshark/searchPage.html.twig', [
            'games' => $games,
        ]);
    }
}
