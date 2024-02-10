<?php

namespace App\DataFixtures;

use App\Entity\Unavailibility;
use App\Entity\User;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use function Symfony\Component\Clock\now;

class UnavailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $troubleMakers = $manager->getRepository(User::class)->findByRole('ROLE_TROUBLE_MAKER');

        foreach ($troubleMakers as $troubleMaker) {
            $for = random_int(1, 4);
            for ($i = 0; $i < $for; $i++) {
                $startDate = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 days', '+15 days'));
                $hours = random_int(1, 8);
                $endDate = $startDate->add(new DateInterval("PT{$hours}H"));

                $unavailability = (new Unavailibility())
                    ->setTroubleMaker($troubleMaker)
                    ->setStartTime($startDate)
                    ->setEndTime($endDate)
                    ->setCreatedAt(now());

                $manager->persist($unavailability);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CompanyFixtures::class,
        ];
    }
}
