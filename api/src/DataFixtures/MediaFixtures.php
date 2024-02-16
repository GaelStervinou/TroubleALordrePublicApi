<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MediaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $companyPictureNames = [
            'company-1.jpeg',
            'company-2.jpeg',
            'company-3.jpeg',
            'company-4.jpeg',
            'company-5.jpeg',
            'gilet-jaune.jpeg',
            'rond-point.jpeg',
        ];

        for ($i = 0; $i < 300; $i++) {
            $manager->persist((new Media())
                ->setPath($faker->randomElement($companyPictureNames)));
        }

        $manager->flush();
    }
}
