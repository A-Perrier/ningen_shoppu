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
      new TwigFunction('getImage', [$this, 'getImage']),
      new TwigFunction('getAsset', [$this, 'getAsset'])
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

  /**
   * Returns the full view path from the filename
   *
   * @param string $filename
   * @param integer $width
   * @param integer $height
   * @return string
   */
  public function getImage(?string $filename = null, int $width, int $height) 
  {
    if (!$filename) return $this->imagePathGenerator->generateAsset("default-placeholder.png", $width, $height);
    
    return $this->imagePathGenerator->generate($filename, $width, $height);
  }

  /**
   * Returns the full view path from the filename
   *
   * @param string $filename
   * @param integer $width
   * @param integer $height
   * @return string
   */
  public function getAsset(string $filename, int $width, int $height) 
  {
    if (!$filename) return;
    
    return $this->imagePathGenerator->generateAsset($filename, $width, $height);
  }
}