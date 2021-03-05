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

  /**
   * Récupère le total monétaire en faisant la somme de chaque article en comprenant la quantité
   * récupérée dans le panier contenu en session
   *
   * @return integer
   */
  public function getTotal()
  {
    $total = 0;

    foreach ($this->getCart() as $id => $quantity) {
      $product = $this->productService->find($id);

      if (!$product) continue;

      $total += ($product->getPrice() * $quantity);
    }

    return $total;
  }


  /**
   * Enlève du panier le produit qui correspond à l'id passé en paramètre
   *
   * @param integer $id
   * @return void
   */
  public function remove(int $id)
  {
    $cart = $this->getCart();
    unset($cart[$id]);

    $this->saveCart($cart);
  }

  /**
   * Vide le panier en supprimant tous les éléments dans le cart de la session
   *
   * @return void
   */
  public function empty()
  {
    $this->saveCart([]);
  }


  /**
   * Baisse d'une unité la quantité du produit passé en id, le supprime s'il n'en restait qu'un seul
   *
   * @param integer $id
   * @return void
   */
  public function decrement(int $id)
  {
    $cart = $this->getCart();

    if(!array_key_exists($id, $cart)){
      return;
    }

    if($cart[$id] === 1){
      $this->remove($id);
      return;
    }
    
    $cart[$id]--;
    
    $this->saveCart($cart);
  }
}