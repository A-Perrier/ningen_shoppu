<?php
namespace App\Service;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class PurchaseService
{
  private $purchaseRepository;
  private $security;

  public function __construct(PurchaseRepository $purchaseRepository, Security $security)
  {
    $this->purchaseRepository = $purchaseRepository;
    $this->security = $security;
  }

  public function find($id)
  {
    return $this->purchaseRepository->find($id);
  }

  public function findLastFromUser()
  {
    $user = $this->security->getUser();
    $purchases = $user->getPurchases()->getValues();
    $lastPurchase = end($purchases);
    
    return $lastPurchase;
  }

  public function getTotal(Purchase $purchase)
  {
    $total = 0;

    foreach ($purchase->getPurchaseItems() as $item)
    {
      $total += $item->getTotal();
    }

    return $total;
  }
}