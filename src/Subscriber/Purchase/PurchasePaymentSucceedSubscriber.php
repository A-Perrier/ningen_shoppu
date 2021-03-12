<?php
namespace App\Subscriber\Purchase;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\PurchasePaymentSucceedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchasePaymentSucceedSubscriber implements EventSubscriberInterface
{
  private $em;
  private $cartService;

  public function __construct(EntityManagerInterface $em, CartService $cartService)
  {
    $this->em = $em;
    $this->cartService = $cartService;
  }

  public static function getSubscribedEvents()
  {
    return [
      Purchase::PAYMENT_SUCCEED_EVENT => [
        ["setPaid", 10],
        ["emptyCart", 9],
        ["decreaseStock", 8]
      ]
    ];
  }

  public function emptyCart()
  {
    $this->cartService->empty();
  }

  public function setPaid(PurchasePaymentSucceedEvent $event)
  {
    $purchase = $event->getPurchase();
    $purchase->setStatus(Purchase::STATUS_PAID);

    $this->em->flush();
  }

  public function decreaseStock(PurchasePaymentSucceedEvent $event)
  {
    $purchase = $event->getPurchase();

    foreach ($purchase->getPurchaseItems()->getValues() as $purchaseItem) {
      /** @var Product */
      $product = $purchaseItem->getProduct();
      $quantity = $purchaseItem->getQuantity();

      $product->setQuantityInStock(($product->getQuantityInStock() - $quantity));

      if($product->getQuantityInStock() == 0) $product->setIsOnSale(false);
    }

    $this->em->flush();
  }
}