<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ProductService;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $slugger;
    private $productService;

    public function __construct(SluggerService $slugger, ProductService $productService)
    {
        $this->slugger = $slugger;
        $this->productService = $productService;
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
     */
    public function create(Request $request): Response
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($this->slugger->slugify($product->getWording()));

            $this->productService->manageImageOnProductCreation($product);

            $this->addFlash("success", "Le produit a correctement été créé !");
            return $this->redirectToRoute("product_maker");
        }

        return $this->render('product/maker.html.twig', [
            'form' => $form->createView(),
            'editing' => false
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
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
            return $this->redirectToRoute('product_edit', [
                'id' => $id
            ]);
        }

        return $this->render('product/maker.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'editing' => true
        ]);
    }
}
