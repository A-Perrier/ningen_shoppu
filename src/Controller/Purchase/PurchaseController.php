<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Event\CartConfirmationEvent;
use App\Service\CartService;
use App\Form\CartConfirmationType;
use App\Service\PurchaseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchaseController extends AbstractController
{
    private $cartService;
    private $purchaseService;
    private $dispatcher;

    public function __construct(CartService $cartService, PurchaseService $purchaseService, EventDispatcherInterface $dispatcher)
    {
        $this->cartService = $cartService;
        $this->dispatcher = $dispatcher;
        $this->purchaseService = $purchaseService;
    }

    /**
     * @Route("/purchase", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function index(Request $request): Response
    {
        $purchase = new Purchase;
        $form = $this->createForm(CartConfirmationType::class, $purchase);
        $form->handleRequest($request);

        $this->cartService->ifCartEmptyThrows();

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new CartConfirmationEvent($purchase);
            $this->dispatcher->dispatch($event, 'purchase.cart_confirmation');
            $this->addFlash("success", "Votre commande a correctement été créée. Vous pouvez procéder au paiement");

            $purchase = $this->purchaseService->findLastFromUser();

            return $this->redirectToRoute("purchase_payment", [
                'id' => $purchase->getId()
            ]);
        }

        return $this->render('purchase/confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/purchase/payment/{id}", name="purchase_payment")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour payer une commande")
     */
    public function payment($id)
    {
        $purchase = $this->purchaseService->find($id);

        if (!in_array($purchase, $this->getUser()->getPurchases()->getValues())) {
            $this->addFlash("danger", "Vous ne possédez aucune commande avec ce numéro");
            return $this->redirectToRoute("cart_show");
        }
        

        return $this->render('purchase/payment.html.twig', [
            'purchase' => $purchase
        ]);
    }
}
