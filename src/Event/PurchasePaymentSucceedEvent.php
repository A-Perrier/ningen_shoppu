<?php
namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasePaymentSucceedEvent extends Event
{
  protected $purchase;

  public function __construct(Purchase $purchase)
  {
    $this->purchase = $purchase;
  }

  public function getPurchase()
  {
    return $this->purchase;
  }
}