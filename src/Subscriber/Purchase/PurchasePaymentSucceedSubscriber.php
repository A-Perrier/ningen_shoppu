<?php
namespace App\Subscriber\Purchase;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\PurchasePaymentSucceedEvent;
use App\Service\CartService;
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
        ["emptyCart", 1],
        ["setPaid", 2]
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
}