<?php

namespace App\DataFixtures;

use App\Entity\Rate;
use App\Entity\Reservation;
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
            $rate = new Rate();
            $rate->setReservation($reservation)
                ->setValue($faker->numberBetween(0, 5))
                ->setCustomer($reservation->getCustomer())
                ->setService($reservation->getService())
                ->setReservation($reservation)
                ->setContent($faker->text(255))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 days', '-4 hours')))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 days', '-4 hours')));
            $manager->persist($rate);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ReservationFixtures::class,
        ];
    }
}