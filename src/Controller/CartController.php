<?php

namespace App\Controller;

use App\Service\CartService;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartController extends AbstractController
{
    private $cartService;
    private $productService;

    public function __construct(CartService $cartService, ProductService $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(): Response
    {
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        return $this->render('cart/cart.html.twig', [
            'detailedCart' => $detailedCart,
            'total' => $total,
        ]);
    }

    /**
     * Affiche la quantité de products dans la navbar
     *
     * @return Response
     */
    public function count(): Response
    {
        $count = $this->cartService->countItems();
        return new Response($count);
    }


    /**
     * @Route("/cart/increment/{id}", name="cart_increment", requirements={"id":"\d+"})
     */
    public function add($id): Response
    {
        $product = $this->productService->findOrThrow($id, "Le produit $id n'existe pas");
 
        if (!$this->cartService->isAddAllowed($product, 1)) {
            $this->addFlash("danger", "Vous avez atteint la limite de notre stock");
            return $this->redirectToRoute("cart_show");
        }
        $this->cartService->add($id, 1);

        $this->addFlash("success", "Le produit a bien été incrémenté au panier !");

        return $this->redirectToRoute("cart_show");
    }


    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id)
    {
        $this->productService->findOrThrow($id, "Le produit $id n'existe pas et ne peut donc être décrémenté du panier");

        $this->cartService->decrement($id);
        $this->addFlash("success", "La quantité du produit a bien été baissée d'une unité");

        return $this->redirectToRoute("cart_show");
    }

    
    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     */
    public function delete($id)
    {
        $this->productService->findOrThrow($id, "Le produit $id n'existe pas et ne peut donc être supprimé du panier");

        $this->cartService->remove($id);
        $this->addFlash("success", "Le produit a correctement été supprimé du panier");

        return $this->redirectToRoute("cart_show");

    }
}
