<?php

namespace App\State\Reservation;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use App\State\CreateAndUpdateStateProcessor;
use Stripe\StripeClient;
use Symfony\Bundle\SecurityBundle\Security;

class CreateReservationSateProcessor implements ProcessorInterface
{
    public function __construct(private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor, private readonly Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof Reservation){
            if ($data->getPaymentIntentId()){
                $stripe = new StripeClient($_ENV['STRIP_SECRET_KEY']);
                $paymentIntent = $stripe->paymentIntents->retrieve(
                    $data->getPaymentIntentId(),
                    []
                );
                if (!isset($paymentIntent['status']) || $paymentIntent['status'] != 'succeeded'){
                    throw new InvalidArgumentException("Erreur de paiement");
                }
            }
            $data->setCustomer($this->security->getUser());
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }else{
            throw new InvalidArgumentException("Erreur de paiement");
        }
    }
}
