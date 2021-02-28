<?php
namespace App\Twig;

use App\Entity\Product;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      new TwigFunction('firstImage', [$this, 'findFirstImageName'])
    ];
  }

  public function findFirstImageName(Product $product)
  {
    if (!$product->getProductImages()->getValues()) return;

    $firstImage = $product->getProductImages()->getValues()[0];
    return $firstImage->getImageName();
  }
}