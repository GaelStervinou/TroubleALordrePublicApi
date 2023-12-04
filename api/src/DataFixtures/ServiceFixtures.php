<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Company;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = $manager->getRepository(Category::class)->findAll();
        $cities = $manager->getRepository(City::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();

        foreach ($companies as $company) {

            $usersCompany = $company->getUsers();

            foreach ($categories as $category) {

                $randomValue = rand(0, 2);

                for ($i = 0; $i < $randomValue; $i++) {
                    $service = new Service();
                    $service->setName($faker->word)
                        ->setCategory($category)
                        ->setCompany($company)
                        ->setDescription($faker->text)
                        ->setDuration(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 hours', '-1 seconds')))
                        ->setPrice($faker->randomFloat(2, 0, 1000))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')));

                    $randomTotalCities = rand(1, 2);
                    for ($j = 0; $j < $randomTotalCities; $j++) {
                        $service->addCity($cities[$j]);
                    }

                    $randomTotalUserService = rand(1, 2);
                    for ($k = 0; $k < $randomTotalUserService; $k++) {
                        $service->addUser($usersCompany[$k]);
                    }

                    $manager->persist($service);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            CityFixtures::class,
            CompanyFixtures::class
        ];
    }
}