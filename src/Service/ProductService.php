<?php
namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
  private $productRepository;
  private $em;

  public function __construct(ProductRepository $productRepository, EntityManagerInterface $em)
  {
    $this->productRepository = $productRepository;
    $this->em = $em;
  }

  public function find($id)
  {
    return $this->productRepository->find($id);
  }

  public function findAll()
  {
    return $this->productRepository->findAll();
  }

  public function findAllOnSale()
  {
    return $this->productRepository->findAllOnSale();
  }

  public function findAllOutSale()
  {
    return $this->productRepository->findBy(["isOnSale" => false], ["id" => "DESC"]);
  }

  public function findBy(array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
  {
    return $this->productRepository->findBy($criteria, $orderBy, $limit, $offset);
  }

  public function findOrThrow(int $id, ?string $message = null)
  {
    $product = $this->find($id);

    if(!$product){
      throw new NotFoundHttpException($message);
    }

    return $product;
  }

  public function findLasts($nb = 5)
  {
    return $this->productRepository->findBy([], ["id" => "DESC"], $nb); 
  }

  public function findLastsOnSale($nb = 5)
  {
    return $this->productRepository->findLastsOnSale($nb); 
  }

  public function manageImageOnProductEdition(Product $product)
  {
    foreach ($product->getProductImages() as $key => $image) {
      // If an image field was requested but sent empty, avoid an error
      if (!$image->getId() && !$image->getImageName() && $image->getImageFile() === null) {
        $product->removeProductImage($image);
      } else {
          // If an image is requesting deletion, removes it before an error is thrown
          if (!$image->getId()) {
              $image->setProduct($product);
              $product->getProductImages()->set($key, $image);
              $this->em->persist($image); 
          } elseif ($image->getId() && !$image->getImageName()) {
              $this->em->remove($image);
          }
      };
    }
    $this->em->flush();
  }

  public function manageImageOnProductCreation(Product $product)
  {
    foreach ($product->getProductImages() as $key => $image) {
      // If an image field was requested but sent empty, avoid an error
      if (!$image->getId() && !$image->getImageName() && $image->getImageFile() === null) {
        $product->removeProductImage($image);
      } else {
          // If an image is requesting deletion, removes it before an error is thrown
              $image->setProduct($product);
              $product->getProductImages()->set($key, $image);
              $this->em->persist($image); 
      };
    }
    $this->em->persist($product);
    $this->em->flush();
  }

  /**
   * Delete a product and all pictures attached on it
   *
   * @param Product $product
   * @return void
   */
  public function remove(Product $product)
  {
    foreach ($product->getProductImages() as $image) {
      $this->em->remove($image);
    }
    $this->em->remove($product);
    $this->em->flush();
  }
  
}