<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use App\Entity\Product;
use App\Service\SluggerService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Liior\Faker\Prices;

class AppFixtures extends Fixture
{
    const ADJECTIVES = [
        'Délicieux', "Merveilleux", "Epoustouflant", "Surprenant", "Enthousiasmant"
    ];

    const SPICES = [
        'Ail', 'Ail Noir', 'Aneth', 'Anis', 'Badiane', 'Cannelle', 'Cardamome', 'Carvi', 'Colombo', 'Coriandre', 'Cumin', 'Curcuma', 'Curry', 'Graines de Fenouil', 'Galanga', 'Garam Masala', 'Gingembre', 'Girofle', 'Kororima', 'Macis', 'Mahaleb', 'Moutarde graines', 'Muscade', 'Nigelle', 'Paprika', 'Pavot', "Piment d'espelette", 'Poivre de Madagascar', "Ras el Hanout", "Réglisse", "Safran", "Sésame", "Sumac", "Vanille de Madagascar", "Wasabi d'Hiroshima"
    ];

    const CATEGORIES = [
        "Condiments", "Epices", "Aromates", "Assaisonnement"
    ];

    private $slugger;

    public function __construct(SluggerService $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));

        for ($c = 0; $c < count(self::CATEGORIES); $c++) {
            $category = new Category;
            $category->setTitle(self::CATEGORIES[$c])
                     ->setSlug($this->slugger->slugify($category->getTitle()))
                     ->setDescription($faker->sentences(3, true))
            ;

            for ($p = 0; $p < 20; $p++) {
                $product = new Product;
                $product->setCategory($category)
                        ->setWording($faker->randomElement(self::ADJECTIVES) . ' ' . $faker->randomElement(self::SPICES))
                        ->setSlug($this->slugger->slugify($product->getWording()))
                        ->setRating(null)
                        ->setPrice($faker->price(100, 2000))
                        ->setDescription($faker->sentences(3, true))
                        ;

                $manager->persist($product);
            }

            $manager->persist($category);
        }

        $manager->flush();
    }
}
