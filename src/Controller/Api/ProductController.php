<?php
namespace App\Controller\Api;

use Exception;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Service\ProductService;
use App\Service\SluggerService;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
  private $productService;
  private $categoryService;
  private $em;
  private $slugger;

  public function __construct(ProductService $productService, CategoryService $categoryService, SluggerService $slugger, EntityManagerInterface $em)
  {
    $this->productService = $productService;
    $this->categoryService = $categoryService;
    $this->slugger = $slugger;
    $this->em = $em;
  }

  /**
   * @Route("/api/product/create", name="api/product_create", methods={"POST"})
   * @IsGranted("ROLE_ADMIN")
   */
  public function create(Request $request, ValidatorInterface $validator): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }

    $data = json_decode($request->getContent());
    $data->category = $this->categoryService->find($data->category);
   
    $product = new Product();
    $product->setWording($data->wording)
            ->setSlug($this->slugger->slugify($product->getWording()))
            ->setDescription($data->description)
            ->setPrice($data->price ?? 0)
            ->setCategory($data->category)
            ->setRating([])
    ;


    $errors = $validator->validate($product);
    $parsedErrors = [];

    if (count($errors) > 0) {

        for ($i = 0; $i < count($errors); $i++) {
            $parsedErrors[$errors->get($i)->getPropertyPath()] = $errors->get($i)->getMessage();
        }
        return $this->json($parsedErrors, 400);

    } else {
        $this->em->persist($product);
        $this->em->flush();

        return $this->json($product->getId(), 201);
    }
    
  }

  /**
   * @Route("/api/product/create/insertPictures/{id}", name="api/product_create_insert_pictures", methods={"POST"})
   * @IsGranted("ROLE_ADMIN")
   */
  public function insertPictures(Request $request, $id): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }

    $product = $this->productService->find($id);
    
    $files = $request->files->get('product')['product_images'] ?? [];
    foreach ($files as $file) {

      /** @var UploadedFile */
      $uploadedFile = $file['imageFile']['file'];

      $productImage = new ProductImage();
      $productImage->setImageFile($uploadedFile);
      $product->addProductImage($productImage);

    }
    
    $this->productService->manageImageOnProductCreation($product);

    return $this->json("", 201);
  }


  /**
   * @Route("/api/product/delete/{id}", name="api/product_delete", methods={"POST"})
   * @IsGranted("ROLE_ADMIN")
   */
  public function delete(Request $request, $id): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }
    
    $product = $this->productService->find($id);
    if (!$product) {
      return $this->json("Aucune donnée n'a été trouvée", 400);
    }

    $this->productService->remove($product);

    return $this->json("Le produit a correctement été supprimé", 200);
  }
}