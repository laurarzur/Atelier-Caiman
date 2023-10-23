<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class FixturesSerie extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $series = [
            1 => [
                'titre' => 'Fresques et tableaux muraux'
            ], 
            2 => [
                'titre' => 'Plaques nom et numéro de maison'
            ],
            3 => [
                'titre' => 'Plaque prénom'
            ],
            4 => [
                'titre' => 'Meubles'
            ]
        ];

        foreach ($series as $ser => $value) {
            $serie = new Serie(); 
            $serie->setTitre($value['titre']); 
            $manager->persist($serie); 
            $this->addReference('serie-' . $ser, $serie);
        }

        $manager->flush();
    }
}
