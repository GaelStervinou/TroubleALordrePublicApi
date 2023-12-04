<?php
// api/src/EventSubscriber/BookMailSubscriber.php
namespace App\EventSubscriber;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class UserMailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerService $mailerService,
    )
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['sendUserValidationMail', EventPriorities::POST_WRITE],
        ];
    }
    public function sendUserValidationMail(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || Request::METHOD_POST !== $method) {
            return;
        }
        $this->mailerService::sendEmail(
            [
                'emailTo' => $user->getEmail(),
                'lastnameTo' => $user->getLastname(),
                'firstnameTo' => $user->getFirstname(),
                'validationToken' => $user->getValidationToken(),
            ],
            MailerService::VERIFY_ACCOUNT_TEMPLATE_ID
        );
    }
}