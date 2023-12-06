<?php

namespace App\Controller\Action\PaymentIntent;

use App\Entity\PaymentIntent;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreatePaymentIntentAction extends AbstractController
{
    public function __construct(
    ) {}

    public function __invoke(PaymentIntent $paymentIntent): PaymentIntent
    {
        $stripe = new StripeClient($_ENV['STRIP_SECRET_KEY']);
        $paymentIntentCreated = $stripe->paymentIntents->create([
            'amount' => 1500,
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        $paymentIntent->setId($paymentIntentCreated['id']);
        $paymentIntent->setClientSecret($paymentIntentCreated['client_secret']);

        return $paymentIntent;
    }
}
