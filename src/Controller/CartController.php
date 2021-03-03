<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(): Response
    {
        $detailedCart = $this->cartService->getDetailedCartItems();

        return $this->render('cart/cart.html.twig', [
            'detailedCart' => $detailedCart
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
}
