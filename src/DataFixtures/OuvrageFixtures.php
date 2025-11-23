<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Exemplaire;
use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OuvrageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        //Ajout des catégories dans la bdd
        $cat = [];
        for ($i = 0; $i < 20; $i++) {
            $categorie = new Categorie();
            $categorie->setCategorieNom($faker->word());
            $manager->persist($categorie);
            $cat[] = $categorie;
        }
        $manager->flush();

        // ajout des auteurs dans la bdd
        $auteurs = [];
        for ($i = 0; $i < 20; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->lastName());
            $auteur->setPrenom($faker->firstName());
            $manager->persist($auteur);
            $auteurs[] = $auteur;
        }
        $manager->flush();

        //Ajout des ouvrages dans la BDD
        $lesOuvrages = [];
        for ($i = 0; $i < 500; $i++) {
            $ouvrage = new Ouvrage();
            $ouvrage->setTitre($faker->realText(25));
            $ouvrage->setResume($faker->paragraphs(3, true));
            $ouvrage->setAnnee($faker->numberBetween(1900, 2025));
            $ouvrage->setIsbn($faker->isbn13());
            $ouvrage->setEditeur($faker->company());
            for ($j = 0; $j < $faker->numberBetween(0,3); $j++) {
                $ouvrage->addCategory($faker->randomElement($cat));
            }

            for ($j = 0; $j < $faker->numberBetween(1,3); $j++) {
                $ouvrage->addAuteur($faker->randomElement($auteurs));
            }
            $lesOuvrages[] = $ouvrage;
            $manager->persist($ouvrage);
        }

        $manager->flush();

        //Ajout des exemplaires dans la BDD
        $etatOuvrage = ["DAMAGED","CORRECT","NEW"];
        for ($j = 0; $j < 1000; $j++) {
            $exemplaire = new Exemplaire();
            $exemplaire->setOuvrage($faker->randomElement($lesOuvrages));
            $exemplaire->setCote($faker->numberBetween(1000,9999));
            $exemplaire->setEtat($faker->randomElement($etatOuvrage));
            $exemplaire->setDisponible($faker->boolean(0.6));
            $manager->persist($exemplaire);
        }
        $manager->flush();

    }
}
