<?php

namespace App\Controller\Action;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MailerService;

class ValidateAccountAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerService $mailerService,
    )
    {}

    #[Route('/validate-account/{token}', name: 'validate_account', methods: ['PATCH'])]
    public function __invoke(string $token): Response
    {
        $user = $this->userRepository->findOneBy(['validationToken' => $token]);
        if(!$user) {
            throw new RuntimeException('User not found');
        }

        $userService = new UserService($user, $this->passwordHasher, $this->mailerService);

        $user = $userService->validateAccount();
        
        try {
            $this->userRepository->save($user, true);
        } catch (\Exception $e) {
            return new Response('Erreur lors de la validation du compte : ' . $e->getMessage());
        }

        return new Response('Compte validé');
    }
}