<?php

namespace App\Controller\Purchase;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    /**
     * @Route("/purchase", name="purchase_confirm")
     */
    public function index(): Response
    {
        return $this->render('purchase/confirm.html.twig', [
            
        ]);
    }
}
