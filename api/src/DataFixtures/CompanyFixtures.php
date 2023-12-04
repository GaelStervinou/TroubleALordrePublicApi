<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\CompanyStatusEnum;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $status = [
            CompanyStatusEnum::ACTIVE,
            CompanyStatusEnum::PENDING,
            CompanyStatusEnum::DELETED,
            CompanyStatusEnum::BANNED
        ];

        $userStatus = [
            UserStatusEnum::USER_STATUS_BANNED,
            UserStatusEnum::USER_STATUS_DELETED,
            UserStatusEnum::USER_STATUS_PENDING,
            UserStatusEnum::USER_STATUS_ACTIVE
        ];

        // pwd = TESTtest@1
        $pwd = '$2y$13$f24/1sWERanDbm00jGHbl.BM39Gsm33CMp7RQcB7Rtl1agoQpSDCa';

        $companyAdmins = $manager->getRepository(User::class)->findByRole('ROLE_COMPANY_ADMIN', false);

        foreach ($companyAdmins as $companyAdmin) {

            $company = new Company();
            $company->setKbis($faker->regexify('[A-Z]{2}[0-9]{3}'))
                ->setStatus($faker->randomElement($status))
                ->setName($faker->company)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-2 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-2 days')));
            $manager->persist($company);

            $companyAdmin->setCompany($company);
            $manager->persist($companyAdmin);

            for ($i = 0; $i < 8; $i++) {
                $user = new User();
                $user->setEmail($faker->email)
                    ->setFirstname($faker->firstName)
                    ->setLastname($faker->lastName)
                    ->setRoles(['ROLE_TROUBLE_MAKER', 'ROLE_USER'])
                    ->setStatus($faker->randomElement($userStatus))
                    ->setPassword($pwd)
                    ->setCompany($company)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));
                $manager->persist($user);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
