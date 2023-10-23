<?php

namespace App\DataFixtures;

use App\Entity\Realisation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class RealisationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
            for ($i = 0; $i < 8; $i++) {
                $serie = $this->getReference('serie-' . $faker->numberBetween(1, 4));
                $realisation = new Realisation();
                $realisation->setNom($faker->sentence);
                $realisation->setDescription($faker->text); 
                $realisation->setImage($faker->imageUrl(300, 200, 'animals', true));
                $realisation->setSerie($serie);
                $manager->persist($realisation); 
            }

        $manager->flush();
    }
}