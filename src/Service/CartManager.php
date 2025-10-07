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

    private function getSessionKey(User $user): string
    {
        return 'cart_' . $user->getId();
    }

    public function addToCart(User $user, array $gameData, string $dealID, int $quantity = 1): void
    {
        $key = $this->getSessionKey($user);
        $cart = $this->session->get($key, []);

        // Si l’item existe déjà, on incrémente
        if (isset($cart[$dealID])) {
            $cart[$dealID]['quantity'] += $quantity;
        } else {
            $cart[$dealID] = [
                'dealID' => $dealID,
                'title' => $gameData['gameInfo']['name'] ?? 'Jeu inconnu',
                'price' => (float) ($gameData['gameInfo']['salePrice'] ?? 0),
                'normalPrice' => (float) ($gameData['gameInfo']['retailPrice'] ?? 0),
                'thumb' => $gameData['gameInfo']['thumb'] ?? '',
                'quantity' => $quantity,
            ];
        }

        $this->session->set($key, $cart);
    }

    public function removeFromCart(User $user, string $dealID): void
    {
        $key = $this->getSessionKey($user);
        $cart = $this->session->get($key, []);

        if (isset($cart[$dealID])) {
            unset($cart[$dealID]);
            $this->session->set($key, $cart);
        }
    }

    public function getCartItems(User $user): array
    {
        return $this->session->get($this->getSessionKey($user), []);
    }

    public function getCartTotal(User $user): float
    {
        $items = $this->getCartItems($user);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clearCart(User $user): void
    {
        $this->session->remove($this->getSessionKey($user));
    }
}
