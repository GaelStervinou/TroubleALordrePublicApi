<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Media;
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
        $medias = $manager->getRepository(Media::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();
        foreach ($companyAdmins as $companyAdmin) {
            $companyMainMedia = $faker->randomElement($medias);
            $companyMedias = $faker->randomElements($medias, 5);

            $company = new Company();
            $company
                ->setStatus($faker->randomElement($status))
                ->setName($faker->company)
                ->setDescription($faker->text(255))
                ->setMainMedia($companyMainMedia)
                ->setAddress($faker->streetAddress)
                ->setZipCode($faker->postcode)
                ->setCity($faker->city)
                ->setLat($faker->latitude)
                ->setLng($faker->longitude)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-2 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', '-2 days')));
            foreach($companyMedias as $companyMedia) {
                $company->addMedia($companyMedia);
            }
            for($i = 0; $i < random_int(1, 5); $i++) {
                $company->addCategory($faker->randomElement($categories));
            }
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
            MediaFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
