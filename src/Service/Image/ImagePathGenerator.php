<?php
namespace App\Service\Image;

use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;

class ImagePathGenerator
{
  /** @var UrlBuilder */
  protected $urlBuilder;

  public function __construct(string $signature)
  {
    $this->urlBuilder = UrlBuilderFactory::create('/images/', $signature);
  }

  public function generate($path, $width, $height): string
  {
    return $this->urlBuilder->getUrl($path, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
  }
}