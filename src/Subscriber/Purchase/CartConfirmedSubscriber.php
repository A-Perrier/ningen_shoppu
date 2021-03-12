<?php
namespace App\Subscriber\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Event\CartConfirmationEvent;
use App\Service\CartService;
use App\Service\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CartConfirmedSubscriber implements EventSubscriberInterface
{
  private $security;
  private $cartService;
  private $purchaseService;
  private $em;

  public function __construct(Security $security, CartService $cartService, PurchaseService $purchaseService, EntityManagerInterface $em)
  {
    $this->security = $security;
    $this->cartService = $cartService;
    $this->purchaseService = $purchaseService;
    $this->em = $em;
  }

  public static function getSubscribedEvents()
  {
    return [
      'purchase.cart_confirmation' => [
        ['setPurchaseItemsAndCompletePurchase', 9]  
      ]
    ];
  }

  /**
   * Set all cartItems into purchaseItems objects and persists them,
   * before filling the purchase and flush
   *
   * @param CartConfirmationEvent $event
   * @return void
   */
  public function setPurchaseItemsAndCompletePurchase(CartConfirmationEvent $event)
  {
    $purchase = $event->getPurchase();

    foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
      $purchaseItem = new PurchaseItem();
      $purchaseItem->setPurchase($purchase)
                   ->setProduct($cartItem->product)
                   ->setProductName($cartItem->product->getWording())
                   ->setProductPrice($cartItem->product->getPrice())
                   ->setQuantity($cartItem->quantity)
                   ->setTotal($cartItem->getTotal())
      ;
      $this->em->persist($purchaseItem);
      $purchase->addPurchaseItem($purchaseItem);
    }


    $purchase->setPurchasedAt(new DateTime())
             ->setStatus(Purchase::STATUS_PENDING)
             ->setUser($this->security->getUser())
             ->setTotal(($this->purchaseService->getTotal($purchase) + Purchase::SHIPPING_FEE))
    ;

    $this->em->persist($purchase);

    $this->em->flush();

    $this->cartService->empty();
  }

}