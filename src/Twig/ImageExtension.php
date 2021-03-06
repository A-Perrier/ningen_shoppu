<?php
namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\Product;
use Twig\Extension\AbstractExtension;
use App\Service\Image\ImagePathGenerator;

class ImageExtension extends AbstractExtension
{
  private $imagePathGenerator;

  public function __construct(ImagePathGenerator $imagePathGenerator)
  {
    $this->imagePathGenerator = $imagePathGenerator;
  }

  public function getFunctions()
  {
    return [
      new TwigFunction('firstImage', [$this, 'findFirstImageName']),
      new TwigFunction('sideImage', [$this, 'findSideImageName']),
      new TwigFunction('getImage', [$this, 'getImage'])
    ];
  }

  public function findFirstImageName(Product $product)
  {
    if (!$product->getProductImages()->getValues()) return;

    $firstImage = $product->getProductImages()->getValues()[0];
    return $firstImage->getImageName();
  }

  public function findSideImageName(Product $product, int $index)
  {
    if (!$product->getProductImages()->getValues()) return;

    $image = $product->getProductImages()->getValues()[$index];
    return $image->getImageName();
  }

  public function getImage(string $path, int $width, int $height) {
    return $this->imagePathGenerator->generate($path, $width, $height);
  }
}