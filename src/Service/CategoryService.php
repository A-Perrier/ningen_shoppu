<?php
namespace App\Service;

use Exception;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
  private $categoryRepository;
  private $productRepository;
  private $em;

  public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, EntityManagerInterface $em)
  {
    $this->categoryRepository = $categoryRepository;
    $this->productRepository = $productRepository;
    $this->em = $em;
  }

  public function find($id)
  {
    return $this->categoryRepository->find($id);
  }

  public function findAll()
  {
    return $this->findBy([], ['title' => "ASC"]);
  }

  public function findBy(array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
  {
    return $this->categoryRepository->findBy($criteria, $orderBy, $limit, $offset);
  }

  public function findLastsProducts(Category $category, $nb = 5)
  {
    return $this->productRepository->findBy(["category" => $category], ["id" => "DESC"], $nb); 
  }


  /**
   * Delete a category
   *
   * @param Category $category
   * @return void
   */
  public function remove(Category $category)
  { 
    foreach ($category->getProducts() as $product) {
      $product->setIsOnSale(false);
    }

    $this->em->remove($category);
    $this->em->flush();
  }
}