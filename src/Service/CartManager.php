<?php 

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToCart(User $user, Game $game, int $quantity = 1): void
    {
        $cart = $this->entityManager->getRepository(Cart::class)
            ->findOneBy(['user' => $user, 'game' => $game]);
            
        if ($cart) {
            $cart->setQuantity($cart->getQuantity() + $quantity);
        } else {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setGame($game);
            $cart->setQuantity($quantity);
            $this->entityManager->persist($cart);
        }
        $this->entityManager->flush();
    }

    public function removeFromCart(User $user, Game $game): void
    {
        $cart = $this->entityManager->getRepository(Cart::class)
            ->findOneBy(['user' => $user, 'game' => $game]);

        if ($cart) {
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        }
    }

    public function getCartItems(User $user): array
    {
        return $this->entityManager->getRepository(Cart::class)
            ->findBy(['user' => $user]);
    }

    public function getCartTotal(User $user): float
    {
        $cartItems = $this->getCartItems($user);
        $total = 0.0;

        foreach ($cartItems as $item) {
            $total += $item->getGame()->getPrice() * $item->getQuantity();
        }

        return $total;
    }

    public function clearCart(User $user): void
    {
        $items = $this->getCartItems($user);

        foreach ($items as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();
    }
}