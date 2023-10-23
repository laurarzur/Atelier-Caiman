<?php 

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

    class CategorieFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            $categories = [
                1 => [
                    'nom' => 'Masques décoratifs', 
                    'slug' => 'masques-decoratifs',
                ], 
                2 => [
                    'nom' => 'Sous verres', 
                    'slug' => 'sous-verres',
                ],
                3 => [
                    'nom' => 'Dessous de plats', 
                    'slug' => 'dessous-plats',
                ]
            ];

            foreach ($categories as $cat => $value) {
                $categorie = new Categorie(); 
                $categorie->setNom($value['nom']); 
                $categorie->setSlug($value['slug']); 
                $manager->persist($categorie); 
                $this->addReference('categorie-' . $cat, $categorie);
            }

            $manager->flush();
        }
    }