<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\ValueObject\RequestResetPasswordLink;
use App\Repository\UserRepository;
use App\Service\MailerService;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RequestResetPasswordAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerService $mailerService,
    )
    {
    }

    public function __invoke(RequestResetPasswordLink $requestResetPasswordLink): RequestResetPasswordLink
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $requestResetPasswordLink->getEmail()]);
        if(!$user) {
            throw new RuntimeException('User not found');
        }
        //TODO envoyer le mail



        $user->setResetPasswordToken(uniqid('', true));
        $this->userRepository->save($user, true);

        return $requestResetPasswordLink->setUrlWithToken($user->getResetPasswordToken());
    }
}