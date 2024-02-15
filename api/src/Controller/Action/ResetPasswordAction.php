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
use Symfony\Component\HttpFoundation\Request;
use App\Service\MailerService;

class ResetPasswordAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerService $mailerService,
    )
    {}

    #[Route('/auth/reset-password/{token}', name: 'reset-password', methods: ['PATCH'])]
    public function __invoke(string $token, Request $request): Response
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        $user = $this->userRepository->findOneBy(['resetPasswordToken' => $token]);
        if(!$user) {
            throw new RuntimeException('User not found');
        }

        $user->setPlainPassword($data['plainPassword']); 
        $user->setVerifyPassword($data['verifyPassword']);

        $userService = new UserService($user, $this->passwordHasher, $this->mailerService);

        $user = $userService->updateUser(false);

        $user->setResetPasswordToken(null);
        
        try {
            $this->userRepository->save($user, true);
        } catch (\Exception $e) {
            return new Response('Erreur lors de la modification du mot de passe : ' . $e->getMessage());
        }

        return new Response('Mot de passe mis à jour !');
    }
}