<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatusEnum;
use App\Http\ImageResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class TroubleMakerReservationsExportStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
    )
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /**@var $loggedInUser User*/
        $loggedInUser = $this->security->getUser();
        $userId = $uriVariables['id'];

        if (!$userId || $userId->toString() !== $loggedInUser?->getId()?->toString()) {
            throw new AccessDeniedException('Vous ne pouvez pas exporter les réservations d\'un autre utilisateur.');
        }
        $userReservations = $this->entityManager->getRepository(Reservation::class)->getTroubleMakerReservations($userId);

        foreach ($userReservations as $index => $reservation) {
            $userReservations[$index]['date'] = $reservation['date']->format('Y-m-d H:i');
            $status = $reservation['status']->value;
            $userReservations[$index]['status'] = $status;
            if (in_array($status, [ReservationStatusEnum::REFUNDED->value, ReservationStatusEnum::CANCELED->value], true)) {
                $userReservations[$index]['duration'] = null;
            } else {

                $userReservations[$index]['duration'] = (string)(floor($reservation['duration']/3600)) . "hours and " . (string)(floor($reservation['duration']/3600) / 60) . ' minutes';
            }
        }


        $csvEncoder = new CsvEncoder();
        $csvData = $csvEncoder->encode($userReservations, 'csv');

        return new Response($csvData);
    }
}
