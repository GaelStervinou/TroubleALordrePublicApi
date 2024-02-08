<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use ApiPlatform\Metadata\ApiResource;

class MailerService
{
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
            ->htmlTemplate($this->getTemplate($templateId));
            // ->context($this->getContext($options, $templateId));

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
                return 'Sujet du mail de vérification';
            case 2:
                return 'Sujet du mail de réinitialisation de mot de passe';
            default:
                return 'Sujet par défaut';
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
        // TODO : context si besoin 
    }
}
