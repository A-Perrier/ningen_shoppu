<?php
namespace App\Controller\Api;

use Exception;
use App\Entity\Category;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
  private $em;
  private $slugger;

  public function __construct(EntityManagerInterface $em, SluggerService $slugger)
  {
    $this->em = $em;
    $this->slugger = $slugger;
  }

  /**
   * @Route("/api/category/create", name="api/category_create")
   * @IsGranted("ROLE_ADMIN")
   */
  public function create(Request $request): Response
  {
    if (!$request->isXmlHttpRequest()) {
      throw new Exception("Une erreur s'est produite", 404);
    }

    $data = json_decode($request->getContent());

    $category = new Category();
    $category->setTitle($data->title)
             ->setSlug($this->slugger->slugify($category->getTitle()))
             ->setDescription($data->description)
    ;
    
    $this->em->persist($category);
    $this->em->flush();

    return $this->json($category);
  }
}