<?php
namespace App\Entity\Unmapped;

use App\Entity\Product;

class CartItem
{
  public $product;
  public $quantity;

  public function __construct(Product $product, int $quantity)
  {
    $this->product = $product;
    $this->quantity = $quantity;
  }
}