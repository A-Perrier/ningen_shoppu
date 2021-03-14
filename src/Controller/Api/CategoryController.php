<?php
namespace App\Controller\Api;

use Exception;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
  private $em;
  private $slugger;
  private $categoryService;

  public function __construct(EntityManagerInterface $em, SluggerService $slugger, CategoryService $categoryService)
  {
    $this->em = $em;
    $this->slugger = $slugger;
    $this->categoryService = $categoryService;
  }

  /**
   * @Route("/api/category/create", name="api/category_create")
   * @IsGranted("ROLE_ADMIN")
   */
  public function create(Request $request, ValidatorInterface $validator): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }

    $data = json_decode($request->getContent());

    $category = new Category();
    $category->setTitle($data->title)
             ->setSlug($this->slugger->slugify($category->getTitle()))
             ->setDescription($data->description)
    ;

    $errors = $validator->validate($category);
    $parsedErrors = [];

    if (count($errors) > 0) {

        for ($i = 0; $i < count($errors); $i++) {
            $parsedErrors[$errors->get($i)->getPropertyPath()] = $errors->get($i)->getMessage();
        }
        return $this->json($parsedErrors, 400);

    } else {
      $this->em->persist($category);
      $this->em->flush();

      return $this->json($category);
    }
    
   }


   /**
   * @Route("/api/category/delete/{id}", name="api/category_delete", methods={"POST"})
   * @IsGranted("ROLE_ADMIN")
   */
  public function delete(Request $request, $id): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 400);
    }
    
    $category = $this->categoryService->find($id);
    if (!$category) {
      return $this->json("Aucune donnée n'a été trouvée", 400);
    }

    foreach($category->getProducts()->getValues() as $product) $product->setIsOnSale(false);
    $this->categoryService->remove($category);
    $this->em->flush();

    return $this->json("La catégorie a correctement été supprimée, et tous les articles qu'elle contenait on été mis hors-ligne", 200);
  }
}