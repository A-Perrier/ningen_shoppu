<?php
namespace App\Controller\Api;

use App\Service\CartService;
use App\Service\ProductService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
  private $productService;
  private $cartService;

  public function __construct(ProductService $productService, CartService $cartService)
  {
    $this->productService = $productService;
    $this->cartService = $cartService;
  }

  /**
   * @Route("/api/cart-add", name="api/cart_add")
   */
  public function add(Request $request): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }

    $data = json_decode($request->getContent());

    $product = $this->productService->find($data->productId);
    if (!$product) {
      return $this->json("Une erreur est survenue", 400);
    }

    $this->cartService->add($data->productId, $data->quantity);

    return $this->json($data);
  }
}