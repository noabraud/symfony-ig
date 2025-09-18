<?php

namespace App\Controller;


use App\Entity\Game;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function showCart(CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $items = $cartManager->getCartItems($user);
        $total = $cartManager->getCartTotal($user);

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
        
    }

    #[Route('/cart/add', name: 'app_cart_add', methods: ['GET'])]
    public function addToCart(Request $request, CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $id = $request->query->get('id'); // récupère ?id=...
        if (!$id) {
            throw $this->createNotFoundException('DealID manquant.');
        }

        $apiUrl = "https://www.cheapshark.com/api/1.0/deals?id=" . $id;
        $response = file_get_contents($apiUrl);
        $gameData = json_decode($response, true);

        if (!$gameData || !isset($gameData['gameInfo'])) {
            throw $this->createNotFoundException('Impossible de récupérer le jeu depuis l’API.');
        }

        
        $cartManager->addToCart($user, $gameData, $id);

        $this->addFlash('success', $gameData['gameInfo']['name'] . ' ajouté au panier !');
        return $this->redirectToRoute('app_cart');
    }




    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function removeFromCart(Request $request, CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        $id = $request->query->get('id'); // récupère ?id=...
        if (!$id) {
            throw $this->createNotFoundException('DealID manquant.');
        }
        $cartManager->removeFromCart($user, $id);
        $this->addFlash('success', 'Jeu retiré du panier !');   

        return $this->redirectToRoute('app_cart');
    }
}