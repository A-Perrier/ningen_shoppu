<?php
namespace App\Service;

use App\Repository\DeliveryRepository;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class DeliveryService
{
  private $deliveryRepository;

  public function __construct(DeliveryRepository $deliveryRepository)
  {
    $this->deliveryRepository = $deliveryRepository;
  }

  public function findBy(?array $criteria = [], ?array $orderBy = [], ?int $limit = null, ?int $offset = null)
  {
    return $this->deliveryRepository->findBy($criteria, $orderBy, $limit, $offset);
  }

  public function findLast()
  {
    return $this->deliveryRepository->findOneBy([], ["id" => "DESC"]);
  }

  public function findOrThrow($id)
  {
    $delivery = $this->deliveryRepository->find($id);

    if (!$delivery) {
      throw new NotFoundResourceException("Cette livraison n'existe pas", 404);
    }

    return $delivery;
  }
}