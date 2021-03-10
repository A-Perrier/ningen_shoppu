<?php
namespace App\Service;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchaseService
{
  private $purchaseRepository;
  private $security;
  private $em;

  public function __construct(PurchaseRepository $purchaseRepository, Security $security, EntityManagerInterface $em)
  {
    $this->purchaseRepository = $purchaseRepository;
    $this->security = $security;
    $this->em = $em;
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

  /**
   * Returns only purchases where its status is paid
   *
   * @return array Purchase
   */
  public function findAllCurrent()
  {
    return $this->purchaseRepository->findAllCurrent();
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

  public function cancel(Purchase $purchase)
  {
    $purchase->setStatus(Purchase::STATUS_CANCELLED);
    $this->em->flush();
  }
}