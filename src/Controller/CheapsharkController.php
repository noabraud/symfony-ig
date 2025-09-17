<?php

namespace App\Controller;

use App\Service\CheapsharkApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CheapsharkController extends AbstractController
{
    public function __construct(RequestStack $requestStack, CheapsharkApi $api)
    {
        $session = $requestStack->getSession();

        $session->set('stores', array_flip($api->getStoresID()));
    }

    #[Route('/', name: 'app_cheapshark_index')]
    public function index(CheapsharkApi $api): Response
    {
        $games = $api->getFrom('deals', ['storeID' => $api->getStoresID(), 'pageSize' => 20, 'sortBy' => 'Savings']);

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
            'deals' => $api->filterDealsByAllowedStores($game['deals'], $api->getStoresID()),
        ]);
    }

    #[Route('/search', name: 'app_cheapshark_search', methods: ['GET'])]
    public function search(Request $request, CheapsharkApi $api): Response
    {
        $title = $request->query->get('title');

        $stores = $request->query->all('stores');
        $page = $request->query->getInt('page', 1);

        $games = $api->getFrom('deals', ['title' => $title, 'storeID' => $api->getStoresID($stores),'pageNumber' => $page - 1, 'pageSize' => 20, 'sortBy' => 'Metacritic']);


        return $this->render('cheapshark/searchPage.html.twig', [
            'games' => $games,
            'currentPage' => $page,
            'title' => $title,
            'stores' => $stores
        ]);
    }
}
