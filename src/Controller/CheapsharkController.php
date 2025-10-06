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
        $new = $api->getFrom('deals', ['storeID' => $api->getStoresID(), 'pageSize' => 8, 'sortBy' => 'Release']);
        $savings = $api->getFrom('deals', ['storeID' => $api->getStoresID(), 'pageSize' => 8, 'sortBy' => 'Savings']);
        $best = $api->getFrom('deals', ['storeID' => $api->getStoresID(), 'pageSize' => 8, 'sortBy' => 'DealRating']);

        return $this->render('cheapshark/index.html.twig', [
            'new' => $new,
            'savings' => $savings,
            'best' => $best,
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
        $stores = $request->query->all('stores');

        $options = [
            'title' => $request->query->getString('title', ''),
            'storeID' => $api->getStoresID($stores),
            'pageNumber' => $request->query->getInt('pageNumber', 0),
            'pageSize' => $request->query->getInt('pageSize', 20),
            'sortBy' => $request->query->getString('sortby', 'DealRating'),
            'desc' => $request->query->getBoolean('desc', false),
            'onSale' => $request->query->getBoolean('onSale', false),
            'steamRating' => $request->query->getInt('note', 0),
            'metacritic' => $request->query->getInt('note', 0),
            'lowerPrice' => $request->query->getInt('lowerPrice', 0),
            'upperPrice' => $request->query->getInt('upperPrice', 500),
        ];
        
        $games = $api->getFrom('deals',  $options);

        return $this->render('cheapshark/searchPage.html.twig', [
            'games' => $games,
            'options' => $options,
            'stores' => $stores,
        ]);
    }
}
