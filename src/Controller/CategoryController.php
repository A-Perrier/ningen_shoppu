<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    private $categoryService;
    private $slugger;
    private $em;

    public function __construct(CategoryService $categoryService, SluggerService $slugger, EntityManagerInterface $em)
    {
        $this->categoryService = $categoryService;
        $this->slugger = $slugger;
        $this->em = $em;
    }

    /**
     * @Route("/category/{id<\d+>}-{slug}", name="category_index")
     */
    public function index($id, $slug): Response
    {
        $category = $this->categoryService->find($id);
        if (!$category) {
            $this->addFlash('danger', "Ce produit n'existe pas");
            return $this->redirectToRoute('home');
        }
        if ($category->getSlug() !== $slug) return $this->redirectToRoute('category_index', ['id' => $id, 'slug' => $category->getSlug()]);


        $lastProducts = $this->categoryService->findLastsProducts($category);

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'lastProducts' => $lastProducts,
        ]);
    }


    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $category->setSlug($this->slugger->slugify($category->getTitle()));

            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash("success", "La catégorie a correctement été créée !");
            return $this->redirectToRoute("category_create");
        }

        return $this->render('category/maker.html.twig', [
            'form' => $form->createView(),
            'editing' => false
        ]);
    }

    /**
     * @Route("/category/edit/{id<\d+>}", name="category_edit")
     */
    public function edit($id, Request $request): Response
    {
        $category = $this->categoryService->find($id);
        if (!$category) {
            $this->addFlash("danger", "Cette catégorie n'existe pas");
            return $this->redirectToRoute("home");
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $category->setSlug($this->slugger->slugify($category->getTitle()));

            $this->em->flush();

            $this->addFlash("success", "La catégorie a correctement été créée !");
            return $this->redirectToRoute("category_index", [
                'id' => $id,
                'slug' => $category->getSlug()
            ]);
        }

        return $this->render('category/maker.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'editing' => true
        ]);
    }
}
