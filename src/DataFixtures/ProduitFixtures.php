<?php 

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker; 

    class ProduitFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            $faker = Faker\Factory::create('fr_FR');
            for ($i = 0; $i < 20; $i++) {
                $categorie = $this->getReference('categorie-' . $faker->numberBetween(1, 3));
                $product = new Produit();
                $product->setTitre($faker->sentence);
                $product->setSlug($faker->slug);
                $product->setDescription($faker->text); 
                $product->setDisponible(true); 
                $product->setPrix($faker->randomFloat(2));
                $product->setImage($faker->imageUrl(300, 200, 'animals', true));
                $product->setCategorie($categorie);
                $manager->persist($product);
            }

           /* foreach ($produits as $prod => $value) {
                $produit = new Produit(); 
                $produit->setTitre($value['titre']); 
                $produit->setSlug($value['slug']); 
                $manager->persist($produit);
            }*/

            $manager->flush();
        }
    }