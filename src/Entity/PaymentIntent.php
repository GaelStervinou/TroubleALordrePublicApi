<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: []
)]
class PaymentIntent
{

    #[Groups(['paymentIntent:read'])]
    private ?String $id = null;

    #[Groups(['paymentIntent:read'])]
    private ?String $client_secret = null;

    public function getId(): ?String
    {
        return $this->id;
    }

    public function setId(String $id): void
    {
        $this->id = $id;
    }

    public function getClientSecret(): ?string
    {
        return $this->client_secret;
    }

    public function setClientSecret(?string $client_secret): void
    {
        $this->client_secret = $client_secret;
    }
}
