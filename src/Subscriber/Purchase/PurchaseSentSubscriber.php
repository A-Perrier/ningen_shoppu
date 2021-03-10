<?php
namespace App\Subscriber\Purchase;

use App\Entity\Purchase;
use App\Event\PurchaseSentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSentSubscriber implements EventSubscriberInterface
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public static function getSubscribedEvents()
  {
    return [
      Purchase::PURCHASE_SENT_EVENT => [
        ['setSent', 2]
      ]
    ];
  }

  public function setSent(PurchaseSentEvent $event)
  {
    $purchase = $event->getPurchase();
    $purchase->setStatus(Purchase::STATUS_SENT);
    $this->em->flush();
  }
}