<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SluggerService
{
  protected $slugger;

  public function __construct(SluggerInterface $sluggerInterface)
  {
    $this->slugger = $sluggerInterface;
  }

  public function slugify($content)
  {
    $content = strtolower($this->slugger->slug($content));
    return $content;
  }
}