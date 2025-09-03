<?php

namespace App\Controller;


use App\Entity\Game;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addToCart(Game $game, CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $cartManager->addToCart($user, $game);
        $this->addFlash('success', 'Jeu ajouté au panier !');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function removeFromCart(Game $game, CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }
        $cartManager->removeFromCart($user, $game);
        $this->addFlash('success', 'Jeu retiré du panier !');   

        return $this->redirectToRoute('app_cart');
    }
}