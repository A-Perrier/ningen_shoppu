<?php
namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class CategoryService
{
  private $categoryRepository;
  private $productRepository;

  public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository)
  {
    $this->categoryRepository = $categoryRepository;
    $this->productRepository = $productRepository;
  }

  public function find($id)
  {
    return $this->categoryRepository->find($id);
  }

  public function findLastsProducts(Category $category, $nb = 5)
  {
    return $this->productRepository->findBy(["category" => $category], ["id" => "DESC"], $nb); 
  }
}