<?php

namespace App\Controller;

use App\Service\StripeManager;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/payment/checkout', name: 'app_payment_checkout')]
    public function checkout(StripeManager $stripeManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $stripeManager->createCheckoutSession(
            $user,
            $this->generateUrl('app_payment_success', [], 0),
            $this->generateUrl('app_cart', [], 0)
        );

        return $this->redirect($session->url);
    }

    #[Route('/payment/success', name: 'app_payment_success')]
    public function paymentSuccess(CartManager $cartManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartManager->clearCart($user);
        $this->addFlash('success', 'Paiement réussi ! Votre panier a été vidé.');

        return $this->redirectToRoute('app_cart');
    }
}
