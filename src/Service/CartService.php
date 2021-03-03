<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
  private $session;

  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }

  public function getCart()
  {
    return $this->session->get('cart', []);
  }

  public function saveCart($cart)
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
}