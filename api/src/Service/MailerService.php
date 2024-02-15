<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;

class MailerService
{
    public const VERIFY_ACCOUNT_TEMPLATE_ID = 1;
    public const RESET_PASSWORD_TEMPLATE_ID = 2;
    
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(array $options, int $templateId = 2): void
    {
        $email = (new TemplatedEmail())
            ->to($options['emailTo'])
            ->subject($this->getSubject($templateId))
            ->from('troublealordrepublic@gmail.com')
            ->htmlTemplate($this->getTemplate($templateId))
            ->context($this->getContext($options, $templateId));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \Exception('Unable to send email: ' . $e->getMessage());
        }
    }

    private function getSubject(int $templateId): string
    {
        // TODO : logique pour récupérer le sujet en fonction du templateId
        switch ($templateId) {
            case 1:
                return 'Trouble à l\'ordre public - Validation de votre compte';
            case 2:
                return 'Trouble à l\'ordre public - Réinitialisation de votre mot de passe';
            default:
                return 'Trouble à l\'ordre public - Information importante';
        }
        
    }

    private function getTemplate(int $templateId): string
    {
        // TODO : gestion template selon le mail
        switch ($templateId) {
            case 1:
                return 'emails/validation.html.twig';
            case 2:
                return 'emails/reset_password.html.twig';
            default:
                return 'emails/default.html.twig';
        }
    }

    private function getContext(array $options, int $templateId): array
    {
        // TODO : logique pour récupérer le contexte en fonction du templateId
        switch ($templateId) {
            case 1:
                $validationLink = 'http://localhost:5173/validate-account/' . $options['validationToken'];

                return [
                    'lastnameTo' => $options['lastnameTo'],
                    'firstnameTo' => $options['firstnameTo'],
                    'validationLink' => $validationLink,
                ];
            case 2:
                $resetLink = 'http://localhost:5173/reset-password/' . $options['resetToken'];

                return [
                    'lastnameTo' => $options['lastnameTo'],
                    'firstnameTo' => $options['firstnameTo'],
                    'resetLink' => $resetLink,
                ];
            default:
                return [
                    'lastnameTo' => $options['lastnameTo'],
                    'firstnameTo' => $options['firstnameTo'],
                    'date' => $options['date'],
                ];
        }
    }
}
