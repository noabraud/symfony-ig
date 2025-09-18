<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

class CartManager
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addToCart(User $user, array $gameData, string $dealID, int $quantity = 1): void
    {
        $key = 'cart_'.$user->getId();
        $cart = $this->session->get($key, []);

        if (isset($cart[$dealID])) {
            $cart[$dealID]['quantity'] += $quantity;
        } else {
            $cart[$dealID] = [
                'title' => $gameData['gameInfo']['name'],
                'price' => (float) $gameData['gameInfo']['salePrice'],
                'normalPrice' => (float) $gameData['gameInfo']['retailPrice'],
                'thumb' => $gameData['gameInfo']['thumb'],
                'quantity' => $quantity,
            ];
        }

        $this->session->set($key, $cart);
    }

    public function removeFromCart(User $user, string $dealID): void
    {
        $key = 'cart_'.$user->getId();
        $cart = $this->session->get($key, []);
        unset($cart[$dealID]);
        $this->session->set($key, $cart);
    }

    public function getCartItems(User $user): array
    {
        return $this->session->get('cart_'.$user->getId(), []);
    }

    public function getCartTotal(User $user): float
    {
        $items = $this->getCartItems($user);
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clearCart(User $user): void
    {
        $this->session->remove('cart_'.$user->getId());
    }
}
