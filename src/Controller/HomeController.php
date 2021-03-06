<?php

namespace App\Controller;

use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $products = $this->productService->findLastsOnSale(12);

        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
