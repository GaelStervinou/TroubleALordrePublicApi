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
        $profilePictureNames = [
            'pp-1.jpeg',
            'pp-2.jpeg',
            'pp-3.jpeg',
            'pp-4.jpeg',
            'pp-5.jpeg',
            'pp-6.jpeg',
        ];

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

        for ($i = 0; $i < 15; $i++) {
            $companyMainMedia = $faker->randomElement($medias);
            $companyMedias = $faker->randomElements($medias, 5);

            $company = new Company();
            $company
                ->setStatus($faker->randomElement($status))
                ->setOwner($faker->randomElement($companyAdmins))
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
            foreach ($companyMedias as $companyMedia) {
                $company->addMedia($companyMedia);
            }
            for ($j = 0; $j < random_int(1, 5); $j++) {
                $company->addCategory($faker->randomElement($categories));
            }

            $manager->persist($company);

            for ($h = 0; $h < 20; $h++) {
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
                $profilePicture = (new Media())
                    ->setPath($faker->randomElement($profilePictureNames));
                $manager->persist($profilePicture);
                $user->setProfilePicture($profilePicture);
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
