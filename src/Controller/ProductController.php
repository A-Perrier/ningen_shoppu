<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ProductService;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public const PER_PAGE = 12;

    private $slugger;
    private $productService;

    public function __construct(SluggerService $slugger, ProductService $productService)
    {
        $this->slugger = $slugger;
        $this->productService = $productService;
    }

    /**
     * @Route("/products", name="products_all")
     */
    public function all(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->productService->findAllOnSale();
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        
        return $this->render('product/all.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{id}-{slug}", name="product_show")
     */
    public function show($id, $slug): Response
    {
        $product = $this->productService->find($id);
        if (!$product) {
            $this->addFlash('danger', "Ce produit n'existe pas");
            return $this->redirectToRoute('home');
        }
        if ($product->getSlug() !== $slug) return $this->redirectToRoute('product_show', ['id' => $id, 'slug' => $product->getSlug()]);

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/product/create", name="product_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($this->slugger->slugify($product->getWording()))
                    ->setIsOnSale(true);
            ;

            $this->productService->manageImageOnProductCreation($product);

            $this->addFlash("success", "Le produit a correctement été créé !");
            return $this->redirectToRoute("product_create");
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, $id): Response
    {
        $product = $this->productService->find($id);
        if (!$product) {
            $this->addFlash('danger', 'Ce produit n\'existe pas');
            return $this->redirectToRoute("home");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($this->slugger->slugify($product->getWording()));
            
            $this->productService->manageImageOnProductEdition($product);

            // This redirection avoid fields to render wrongly
            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

}
