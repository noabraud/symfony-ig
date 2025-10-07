<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\StripeManager;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PaymentController extends AbstractController
{
    #[Route('/payment/checkout', name: 'app_payment_checkout')]
    public function checkout(StripeManager $stripeManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // URLs locales HTTP pour dev
        $successUrl = 'http://localhost:8000/payment/success';
        $cancelUrl = 'http://localhost:8000/cart';

        $session = $stripeManager->createCheckoutSession($user, $successUrl, $cancelUrl);

        return $this->redirect($session->url);
    }

    #[Route('/payment/success', name: 'app_payment_success')]
    public function paymentSuccess(CartManager $cartManager, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $cartManager->getCartItems($user);
        $total = $cartManager->getCartTotal($user);

        $order = new Order();
        $order->setUser($user);
        $order->setTotal($total);
        $order->setOrderNumber('CMD-' . strtoupper(substr(md5(uniqid()), 0, 8)));

        foreach ($cartItems as $dealID => $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrderItem($order);
            $orderItem->setGameId($dealID); // dealID depuis le panier
            $orderItem->setGameTitle($cartItem['title'] ?? 'Jeu inconnu');
            $orderItem->setPrice($cartItem['price'] ?? 0);
            $orderItem->setQuantity($cartItem['quantity'] ?? 1);

            $em->persist($orderItem);
        }

        $em->persist($order);
        $em->flush();

        $cartManager->clearCart($user);

        $message = $translator->trans('payment_success.flash', [], 'messages');
        $this->addFlash('success2', $message);

        return $this->render('payment/success.html.twig');
    }
}
