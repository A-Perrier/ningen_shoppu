<?php
namespace App\Subscriber\Delivery;

use DateTime;
use App\Entity\Product;
use App\Entity\Delivery;
use App\Entity\DeliveryItem;
use App\Service\ProductService;
use App\Service\DeliveryService;
use App\Event\DeliveryCreateEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeliveryCreateSubscriber implements EventSubscriberInterface
{
  private $em;
  private $productService;
  private $deliveryService;

  public function __construct(EntityManagerInterface $em, ProductService $productService, DeliveryService $deliveryService)
  {
    $this->em = $em;
    $this->productService = $productService;
    $this->deliveryService = $deliveryService;
  }

  public static function getSubscribedEvents()
  {
    return [
      Delivery::DELIVERY_CREATE_EVENT => [
        ['create', 10],
        ['incrementStock', 9]
      ]
    ];
  }

  /**
   * Unserialize carrier name and set the delivery up
   *
   * @param DeliveryCreateEvent $event
   * @return Delivery $delivery
   */
  private function initializeDelivery(DeliveryCreateEvent $event)
  {
    $delivery = new Delivery();
    $delivery->setCarrier(json_decode($event->getData()->carrier))
             ->setDeliveredAt(new DateTime());

    $this->em->persist($delivery);

    return $delivery;
  }


  /**
   * Unserialize data and turns them into DeliveryItems objects
   *
   * @param DeliveryCreateEvent $event
   * @return Delivery $delivery
   */
  private function initializeDeliveryItems(DeliveryCreateEvent $event)
  {
    $deliveryItems = json_decode($event->getData()->deliveryItems);
    $delivery = $this->initializeDelivery($event);

    foreach ($deliveryItems as $productName => $quantity) {
      $product = $this->productService->findOneBy(['wording' => $productName]);
      
      $deliveryItem = new DeliveryItem();
      $deliveryItem->setProduct($product)
                   ->setQuantity($quantity)
                   ->setDelivery($delivery);

      $delivery->addDeliveryItem($deliveryItem);

      $this->em->persist($deliveryItem);
    }

    return $delivery;
  }


  /**
   * Insert Delivery and DeliveryItems into the database
   *
   * @param DeliveryCreateEvent $event
   * @return Delivery $delivery
   */
  public function create(DeliveryCreateEvent $event)
  {
    $delivery = $this->initializeDeliveryItems($event);

    $this->em->flush();

    return $delivery;
  }


  /**
   * Takes the last delivery and increment stock considering quantity values into DeliveryItems objects
   *
   * @return void
   */
  public function incrementStock()
  {
    $delivery = $this->deliveryService->findLast();

    foreach ($delivery->getDeliveryItems() as $deliveryItem) {
      /** @var Product */
      $product = $deliveryItem->getProduct();
      $quantity = $deliveryItem->getQuantity();
      $product->setQuantityInStock(($product->getQuantityInStock() + $quantity))
              ->setIsOnSale(true);
      ;
    }

    $this->em->flush();
  }
}