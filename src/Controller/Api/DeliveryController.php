<?php
namespace App\Controller\Api;

use App\Entity\Delivery;
use App\Event\DeliveryCreateEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeliveryController extends AbstractController
{

  /**
   * @Route("/api/delivery/create", name="api/delivery_create", methods={"POST"})
   * @IsGranted("ROLE_ADMIN")
   */
  public function addDelivery(Request $request, EventDispatcherInterface $dispatcher): Response
  {
    if (!$request->isXmlHttpRequest()) throw new \Exception("Une erreur s'est produite", 400);
    
    $data = json_decode($request->getContent());

    $dispatcher->dispatch(new DeliveryCreateEvent($data), Delivery::DELIVERY_CREATE_EVENT);

    return $this->json("La livraison a correctement été entrée. Les stocks ont été mis à jour.");
  }
}