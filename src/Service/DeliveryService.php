<?php
namespace App\Service;

use App\Repository\DeliveryRepository;

class DeliveryService
{
  private $deliveryRepository;

  public function __construct(DeliveryRepository $deliveryRepository)
  {
    $this->deliveryRepository = $deliveryRepository;
  }

  public function findLast()
  {
    return $this->deliveryRepository->findOneBy([], ["id" => "DESC"]);
  }
}