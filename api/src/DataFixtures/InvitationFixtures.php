<?php

namespace App\DataFixtures;
use ApiPlatform\Metadata\GraphQl\Query;
use App\Entity\Company;
use App\Entity\Invitation;
use App\Entity\User;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvitationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $companies = $manager->getRepository(Company::class)->findAll();

        $status = [
            UserStatusEnum::USER_STATUS_BANNED,
            UserStatusEnum::USER_STATUS_DELETED,
            UserStatusEnum::USER_STATUS_PENDING,
            UserStatusEnum::USER_STATUS_ACTIVE
        ];

        // pwd = TESTtest@1
        $pwd = '$2y$13$f24/1sWERanDbm00jGHbl.BM39Gsm33CMp7RQcB7Rtl1agoQpSDCa';

        foreach ($companies as $company) {

            $randomValue = rand(0, 3);

            for ($i = 0; $i < $randomValue; $i++) {
                $user = new User();
                $user->setEmail($faker->email)
                    ->setFirstname($faker->firstName)
                    ->setLastname($faker->lastName)
                    ->setRoles(['ROLE_USER'])
                    ->setStatus($faker->randomElement($status))
                    ->setPassword($pwd)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));
                $manager->persist($user);

                $invitation = new Invitation();
                $invitation->setCompany($company)
                    ->setReceiver($user)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-4 days')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-4 days')));
                $manager->persist($invitation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            MediaFixtures::class
        ];
    }
}