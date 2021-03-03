<?php
namespace App\Service;

use App\Entity\Unmapped\CartItem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
  private $session;
  private $productService;

  public function __construct(SessionInterface $session, ProductService $productService)
  {
    $this->session = $session;
    $this->productService = $productService;
  }

  private function getCart()
  {
    return $this->session->get('cart', []);
  }

  private function saveCart($cart)
  {
    $this->session->set('cart', $cart);
  }

  public function add(int $id, int $quantity = null)
  {
    $cart = $this->getCart();

    if (!array_key_exists($id, $cart)) {
      $cart[$id] = 0;
    }

    $cart[$id] += $quantity;

    $this->saveCart($cart);
  }

  public function countItems()
  {
    $cart = $this->getCart();

    $count = 0;
    foreach($cart as $id => $quantity) {
      $count += $quantity;
    }

    return $count;
  }

  /**
   * Permet de récupérer sous forme de tableau d'objets CartItem tous les produits
   * contenu dans le panier par la session.
   * Chaque CartItem est récupéré en base de données et contient l'ensemble des caractéristiques
   * du produit.
   *
   * @return CartItem[]
   */
  public function getDetailedCartItems()
  {
    $detailedItems = [];

    foreach ($this->getCart() as $id => $quantity) {
      $product = $this->productService->find($id);

      if(!$product) continue;

      $detailedItems[] = new CartItem($product, $quantity);
    }

    return $detailedItems;
  }
}