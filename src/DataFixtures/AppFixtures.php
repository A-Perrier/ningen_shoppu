<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Delivery;
use App\Entity\DeliveryItem;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Service\SluggerService;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const PURCHASE_STATUS = [Purchase::STATUS_CANCELLED, Purchase::STATUS_PAID, Purchase::STATUS_PENDING, Purchase::STATUS_SENT];

    private $slugger;
    private $encoder;

    public function __construct(SluggerService $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));

        $products = [];

        // Créer des catégories
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setTitle($faker->department())
                     ->setSlug($this->slugger->slugify($category->getTitle()))
                     ->setDescription($faker->sentence())
            ;

            $manager->persist($category);

            // Y créer des produits
            for ($j = 0; $j < mt_rand(5, 15); $j++) {
                $product = new Product();
                $product->setCategory($category)
                        ->setWording($faker->productName())
                        ->setSlug($this->slugger->slugify($product->getWording()))
                        ->setDescription($faker->sentences(3, true))
                        ->setIsOnSale($faker->boolean(80))
                        ->setPrice($faker->price(2000, 10000))
                        ->setQuantityInStock(mt_rand(1, 80))
                ;

                $products[] = $product;

                $manager->persist($product);
            }
        }
            
        
        // Créer des utilisateurs
        $admin = new User();
        $admin->setEmail("admin@gmail.com")
              ->setPassword($this->encoder->encodePassword($admin, 'password'))
              ->setFirstName($faker->firstName())
              ->setLastName($faker->lastName)
              ->setIsVerified(true)
              ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($admin);

        for ($k = 0; $k < 5; $k++) {
            $user = new User();
            $user->setEmail("user$k@gmail.com")
                 ->setPassword($this->encoder->encodePassword($user, 'password'))
                 ->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName())
                 ->setIsVerified(true)
                 ->setRoles([])
            ;
            $manager->persist($user);


            // Y donner des Purchases
            for ($l = 0; $l < mt_rand(1, 5); $l++) {
                $purchase = new Purchase();
                $purchase->setUser($user)
                         ->setAddress($faker->streetAddress)
                         ->setPostalCode($faker->postcode)
                         ->setCity($faker->city)
                         ->setFullName($user->getFirstName() . ' ' . $user->getLastName())
                         ->setPurchasedAt($faker->dateTimeBetween("-6 months"))
                         ->setStatus($faker->randomElement(self::PURCHASE_STATUS))
                ;
                
                // Y donner des PurchaseItems
                for ($m = 0; $m < mt_rand(1, 15); $m++) {
                    $purchaseItem = new PurchaseItem();
                    $purchaseItem->setProduct($faker->randomElement($products))
                                 ->setProductName($purchaseItem->getProduct()->getWording())
                                 ->setProductPrice($purchaseItem->getProduct()->getPrice())
                                 ->setQuantity(mt_rand(1, 5))
                                 ->setTotal(($purchaseItem->getProductPrice() * $purchaseItem->getQuantity()))
                                 ->setPurchase($purchase);
                    
                    $purchase->addPurchaseItem($purchaseItem);
                    $manager->persist($purchaseItem);
                }

                $total = 0;
                foreach ($purchase->getPurchaseItems() as $purchaseItem) {
                    $total += $purchaseItem->getTotal();
                }

                $purchase->setTotal($total);
                $manager->persist($purchase);
            }
        }
            
        
        // Créer des Deliveries      
        for ($n = 0; $n < mt_rand(5, 10); $n++) {
            $delivery = new Delivery();
            $delivery->setCarrier($faker->company)
                     ->setDeliveredAt($faker->dateTimeBetween("-6 months"))
            ;    

            // Y donner des DeliveryItems
            for ($o = 0; $o < mt_rand(3, 20); $o++) {
                $deliveryItem = new DeliveryItem();
                $deliveryItem->setDelivery($delivery)
                             ->setProduct($faker->randomElement($products))
                             ->setQuantity(mt_rand(6, 24))
                ;
                $delivery->addDeliveryItem($deliveryItem);
                $manager->persist($deliveryItem);
            }

            $manager->persist($delivery);
        }            

        $manager->flush();
    }
}
