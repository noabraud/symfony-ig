<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OrderItemRepository;

final class UserGamesController extends AbstractController
{
    #[Route('/user/games', name: 'app_user_games')]
    public function userGames(OrderItemRepository $orderItemRepository): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $games = $orderItemRepository->findByUser($user);

    return $this->render('user_games/index.html.twig', [
        'games' => $games,
    ]);
}
}
