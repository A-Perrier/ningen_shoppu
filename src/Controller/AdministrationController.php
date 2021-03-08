<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\Service\ProductService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdministrationController extends AbstractController
{
    const PER_PAGE = 24;

    private $productService;
    private $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/administration", name="administration")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('administration/administration.html.twig', [
            
        ]);
    }


    /**
     * @Route("/administration/products", name="products_listing")
     * @IsGranted("ROLE_ADMIN")
     */
    public function productsListing(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->productService->findBy([], ["id" => "DESC"]);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('administration/products-listing.html.twig', [
            'products' => $products,
            'outsale' => false
        ]);
    }


    /**
     * @Route("/administration/products/outsale", name="outsale_listing")
     * @IsGranted("ROLE_ADMIN")
     */
    public function outsaleListing(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->productService->findAllOutSale();
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('administration/products-listing.html.twig', [
            'products' => $products,
            'outsale' => true
        ]);
    }


    /**
     * @Route("/administration/categories", name="categories_listing")
     * @IsGranted("ROLE_ADMIN")
     */
    public function categoriesListing(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->categoryService->findBy([], ["id" => "DESC"]);
        $categories = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('administration/categories-listing.html.twig', [
            'categories' => $categories
        ]);
    }
}
