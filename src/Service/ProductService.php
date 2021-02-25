<?php
namespace App\Service;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
  private $productRepository;
  private $em;

  public function __construct(ProductRepository $productRepository, EntityManagerInterface $em)
  {
    $this->productRepository = $productRepository;
    $this->em = $em;
  }

  public function findLasts($nb = 5)
  {
    return $this->productRepository->findBy([], ["id" => "DESC"], $nb); 
  }
}