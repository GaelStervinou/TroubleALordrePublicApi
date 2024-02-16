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
            'casseur-1.jpeg',
            'casseur-2.jpeg',
        ];
        $coordinates = [
            [
                'lng' => 48.793837,
                'lat' => 2.377474
            ],
            [
                'lng' => 48.745961,
                'lat' => 2.390549
            ],
            [
                'lng' => 48.770691,
                'lat' => 2.113038
            ],
            [
                'lng' => 48.868674,
                'lat' => 3.418726
            ],
            [
                'lng' => 48.860413,
                'lat' => 2.283232
            ],
            [
                'lng' => 48.813995,
                'lat' => 2.273107
            ],
            [
                'lng' => 48.790540,
                'lat' => 2.320239
            ],
            [
                'lng' => 48.926519,
                'lat' => 2.372607
            ],
            [
                'lng' => 48.894624,
                'lat' => 2.262983
            ],
            [
                'lng' => 48.897149,
                'lat' => 2.380637
            ],
            [
                'lng' => 48.865236,
                'lat' => 2.382557
            ],
            [
                'lng' => 48.868911,
                'lat' => 2.456222
            ],
            [
                'lng' => 48.861446,
                'lat' => 2.196824
            ],
            [
                'lng' => 48.926633,
                'lat' => 2.173433
            ],
            [
                'lng' => 49.008914,
                'lat' => 2.199966
            ]
        ];

        $userStatus = [
            UserStatusEnum::USER_STATUS_ACTIVE,
            UserStatusEnum::USER_STATUS_ACTIVE,
            UserStatusEnum::USER_STATUS_ACTIVE,
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
                ->setStatus(CompanyStatusEnum::ACTIVE)
                ->setOwner($faker->randomElement($companyAdmins))
                ->setName($faker->company)
                ->setDescription($faker->text(255))
                ->setMainMedia($companyMainMedia)
                ->setAddress($faker->streetAddress)
                ->setZipCode($faker->postcode)
                ->setCity($faker->city)
                //we inverse
                ->setLat($faker->randomElement($coordinates)['lng'])
                ->setLng($faker->randomElement($coordinates)['lat'])
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
