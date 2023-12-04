<?php

namespace App\Entity\ValueObject;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\RequestResetPasswordAction;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/auth/request-reset-password-link',
            status: 201,
            controller: RequestResetPasswordAction::class,
            input: RequestResetPasswordLink::class,
            output: RequestResetPasswordLink::class,
            name: 'request-reset-password-link',
        )
    ]
)]
class RequestResetPasswordLink
{
    private ?string $url;
    public function __construct(
        private string $email,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setEmail(string $email): RequestResetPasswordLink
    {
        $this->email = $email;
        return $this;
    }

    public function setUrl(string $url): RequestResetPasswordLink
    {
        $this->url = $url;
        return $this;
    }

    public function setUrlWithToken(string $token): RequestResetPasswordLink
    {
        //TODO peut-être changer ça pour mettre le lien de l'app ( front )
        $this->url = $_ENV['APP_URL'] . '/reset-password?token=' . $token;
        return $this;
    }
}