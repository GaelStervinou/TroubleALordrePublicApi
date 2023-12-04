<?php

namespace App\DataFixtures;
use ApiPlatform\Metadata\GraphQl\Query;
use App\Entity\Company;
use App\Entity\Invitation;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $reservations = $manager->getRepository(Reservation::class)->findAll();

        foreach ($reservations as $reservation) {

            $rateTypes = $reservation->getService()->getCategory()->getRateTypes();

            foreach ($rateTypes as $rateType) {
                $rate = new Rate();
                $rate->setReservation($reservation)
                    ->setRateType($rateType)
                    ->setValue($faker->numberBetween(0, 5))
                    ->setCustomer($reservation->getCustomer())
                    ->setService($reservation->getService())
                    ->setReservation($reservation);
                $manager->persist($rate);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ReservationFixtures::class,
            RateTypeFixtures::class
        ];
    }
}