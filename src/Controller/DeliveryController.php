<?php

namespace App\Controller;

use App\Service\ProductService;
use App\Service\DeliveryService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DeliveryController extends AbstractController
{
    private const PER_PAGE = 12;

    private $productService;
    private $deliveryService;

    public function __construct(ProductService $productService, DeliveryService $deliveryService)
    {
        $this->productService = $productService;
        $this->deliveryService = $deliveryService;
    }

    /**
     * @Route("/delivery/create", name="delivery_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->productService->findAll();
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('administration/delivery/create.html.twig', [
            'products' => $products
        ]);
    }
}