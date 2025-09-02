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
        $games = $api->getFrom('deals', ['storeID' => '1,7,8,13,25,31', 'pageSize' => '20', 'sortBy' => 'Savings']);

        return $this->render('cheapshark/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game', name: 'app_cheapshark', methods: ['GET'])]
    public function game(Request $request, CheapsharkApi $api): Response
    {
        $id = $request->query->get('id');

        if ($id) {
            $game = $api->getFrom('games', ['id' => $id]);
        }

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
            $games = $api->getFrom('deals', ['title' => $title, 'storeID' => '1,7,8,13,25,31', 'pageSize' => '20', 'sortBy' => 'Metacritic']);
        }

        return $this->render('cheapshark/searchPage.html.twig', [
            'games' => $games,
        ]);
    }
}
