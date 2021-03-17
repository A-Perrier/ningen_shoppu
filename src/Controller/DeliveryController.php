<?php

namespace App\Controller;

use App\Repository\ProductRepository;
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
    public function create(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->productService->findAll();
        $products = $paginator->paginate(
            $this->productService->findAllQuery(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('administration/delivery/create.html.twig', [
            'products' => $products
        ]);
    }


    /**
     * @Route("/deliveries", name="delivery_listing")
     * @IsGranted("ROLE_ADMIN")
     */
    public function all(): Response
    {
        $deliveries = $this->deliveryService->findBy([], ['id' => "DESC"]);

        return $this->render('administration/delivery/delivery-listing.html.twig', [
            'deliveries' => $deliveries
        ]);
    }


    /**
     * @Route("/delivery/{id}", name="delivery_show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show($id): Response
    {
        $delivery = $this->deliveryService->findOrThrow($id);

        return $this->render('administration/delivery/show.html.twig', [
            'delivery' => $delivery
        ]);
    }
}
