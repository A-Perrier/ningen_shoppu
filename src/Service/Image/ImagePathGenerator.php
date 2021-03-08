<?php
namespace App\Service\Image;

use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;

class ImagePathGenerator
{
  /** @var UrlBuilder */
  protected $urlBuilder;

  /** @var UrlBuiler */
  protected $urlBuilderForAsset;

  public function __construct(string $signature)
  {
    $this->urlBuilder = UrlBuilderFactory::create('/images/', $signature);
    $this->urlBuilderForAsset = UrlBuilderFactory::create('/assets/', $signature);
  }


  /**
   * Returns the entire view url from the file path
   *
   * @param string $path
   * @param integer $width
   * @param integer $height
   * @return string
   */
  public function generate(string $path, int $width, int $height): string
  {
    return $this->urlBuilder->getUrl($path, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
  }

  /**
   * Returns the entire view url from the file path
   *
   * @param string $path
   * @param integer $width
   * @param integer $height
   * @return string
   */
  public function generateAsset(string $path, int $width, int $height): string
  {
    return $this->urlBuilderForAsset->getUrl($path, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
  }
}