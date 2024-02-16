<?php

namespace App\DataFixtures;

use App\Entity\Media;
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
        $profilePictureNames = [
            'pp-1.jpeg',
            'pp-2.jpeg',
            'pp-3.jpeg',
            'pp-4.jpeg',
            'pp-5.jpeg',
            'pp-6.jpeg',
        ];

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
        $user = new User();
        $user->setEmail('gael@gmail.com')
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_USER'])
            ->setStatus(UserStatusEnum::USER_STATUS_ACTIVE)
            ->setPassword($pwd)
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));

        $profilePicture = (new Media())
            ->setPath($faker->randomElement($profilePictureNames));
        $manager->persist($profilePicture);
        $user->setProfilePicture($profilePicture);
        $manager->persist($user);

        $userCompany = new User();
        $userCompany->setEmail('rui@gmail.com')
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_USER', 'ROLE_COMPANY_ADMIN'])
            ->setStatus(UserStatusEnum::USER_STATUS_ACTIVE)
            ->setPassword($pwd)
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));

        $profilePicture = (new Media())
            ->setPath($faker->randomElement($profilePictureNames));
        $manager->persist($profilePicture);
        $userCompany->setProfilePicture($profilePicture);
        $manager->persist($userCompany);

        $userAdmin = new User();
        $userAdmin->setEmail('louis@gmail.com')
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setStatus(UserStatusEnum::USER_STATUS_ACTIVE)
            ->setPassword($pwd)
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));

        $profilePicture = (new Media())
            ->setPath($faker->randomElement($profilePictureNames));
        $manager->persist($profilePicture);
        $userAdmin->setProfilePicture($profilePicture);
        $manager->persist($userAdmin);

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

            $profilePicture = (new Media())
                ->setPath($faker->randomElement($profilePictureNames));
            $manager->persist($profilePicture);
            $user->setProfilePicture($profilePicture);
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

                $profilePicture = (new Media())
                    ->setPath($faker->randomElement($profilePictureNames));
                $manager->persist($profilePicture);
                $user->setProfilePicture($profilePicture);
                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
