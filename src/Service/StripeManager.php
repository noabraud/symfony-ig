<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Entity\User;

class StripeManager
{
    private CartManager $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);
    }

    public function createCheckoutSession(User $user, string $successUrl, string $cancelUrl): Session
    {
        $items = $this->cartManager->getCartItems($user);
        $lineItems = [];

        foreach ($items as $dealID => $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['title'],
                        'metadata' => [
                            'deal_id' => $dealID, // garde le dealID
                        ],
                    ],
                    'unit_amount' => (int)($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }
}
