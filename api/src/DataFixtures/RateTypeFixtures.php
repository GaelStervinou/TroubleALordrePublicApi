<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\RateType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RateTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = $manager->getRepository(Category::class)->findAll();

        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                $rateType = new RateType();
                $rateType->setLabel($faker->word)
                    ->setCategory($category)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-4 months')));
                $manager->persist($rateType);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }
}