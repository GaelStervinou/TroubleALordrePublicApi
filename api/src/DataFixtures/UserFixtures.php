<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $status = [
            UserStatusEnum::USER_STATUS_BANNED,
            UserStatusEnum::USER_STATUS_DELETED,
            UserStatusEnum::USER_STATUS_PENDING,
            UserStatusEnum::USER_STATUS_ACTIVE
        ];

        $roles = [
            UserRolesEnum::ADMIN,
            UserRolesEnum::COMPANY_ADMIN,
        ];

        // pwd = TESTtest@1
        $pwd = '$2y$13$f24/1sWERanDbm00jGHbl.BM39Gsm33CMp7RQcB7Rtl1agoQpSDCa';

        for ($i = 0; $i < 6; $i++) {
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
        }

        foreach ($roles as $role) {
            for ($i = 0; $i < 6; $i++) {
                $user = new User();
                if($role) {
                    $user->setKbis($faker->regexify('[A-Z]{2}[0-9]{3}'));
                }
                $user->setEmail($faker->email)
                    ->setFirstname($faker->firstName)
                    ->setLastname($faker->lastName)
                    ->setRoles([$role->value, 'ROLE_USER'])
                    ->setStatus($faker->randomElement($status))
                    ->setPassword($pwd)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));
                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
