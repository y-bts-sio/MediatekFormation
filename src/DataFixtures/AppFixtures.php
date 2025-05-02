<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe AppFixtures
 * 
 * Fixture de base. Peut être utilisée pour charger des données communes.
 */
class AppFixtures extends Fixture
{
    /**
     * Charge les données de démonstration initiales.
     *
     * @param ObjectManager $manager Gestionnaire des entités
     */
    public function load(ObjectManager $manager): void
    {
        // Exemple de fixture : ajouter ici vos entités
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
