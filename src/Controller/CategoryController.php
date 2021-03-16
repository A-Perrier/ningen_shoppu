<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\SluggerService;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/categories", name="categories_all")
     */
    public function all(): Response
    {
        return $this->render('category/all.html.twig', [
            'categories' => $this->categoryService->findAll()
        ]);
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
     * @IsGranted("ROLE_ADMIN")
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

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/edit/{id<\d+>}", name="category_edit")
     * @IsGranted("ROLE_ADMIN")
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

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
