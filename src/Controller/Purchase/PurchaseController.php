<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use App\Entity\Purchase;
use App\Service\CartService;
use App\Service\StripeService;
use App\Event\PurchaseSentEvent;
use App\Service\PurchaseService;
use App\Form\CartConfirmationType;
use App\Event\CartConfirmationEvent;
use App\Event\PurchasePaymentSucceedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseController extends AbstractController
{
    private $cartService;
    private $purchaseService;
    private $stripeService;
    private $dispatcher;

    public function __construct(CartService $cartService, 
                                PurchaseService $purchaseService, 
                                StripeService $stripeService,
                                EventDispatcherInterface $dispatcher)
    {
        $this->cartService = $cartService;
        $this->dispatcher = $dispatcher;
        $this->purchaseService = $purchaseService;
        $this->stripeService = $stripeService;
    }


    /**
     * @Route("/my-purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour regarder vos commandes")
     */
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();
        $purchases = $user->getPurchases();

        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases
        ]);
    }


    /**
     * @Route("/purchases/{id}", name="purchase_show")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour regarder vos commandes")
     */
    public function show($id): Response
    {
        /** @var User */
        $user = $this->getUser();
        $purchase = $this->purchaseService->find($id);

        if (!in_array($purchase, $user->getPurchases()->getValues())
            || !$purchase) {
                $this->addFlash('danger', 'Vous ne possédez pas de commande avec cet identifiant');
                return $this->redirectToRoute("user_home");
            }


        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase
        ]);
    }


    /**
     * @Route("/purchase", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request): Response
    {
        $purchase = new Purchase;
        $form = $this->createForm(CartConfirmationType::class, $purchase);
        $form->handleRequest($request);

        $this->cartService->ifCartEmptyThrows();

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new CartConfirmationEvent($purchase);
            $this->dispatcher->dispatch($event, Purchase::CART_CONFIRMATION_EVENT);
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

        if (!$purchase 
            || !in_array($purchase, $this->getUser()->getPurchases()->getValues())
            || $purchase->getStatus() !== Purchase::STATUS_PENDING) {
            $this->addFlash("danger", "Vous ne possédez aucune commande en cours avec ce numéro");
            return $this->redirectToRoute("cart_show");
        }
        
        $paymentIntent = $this->stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'purchase' => $purchase,
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }


    /**
     * @Route("/purchase/payment/succeed/{id}", name="purchase_payment_succeed")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour payer une commande")
     */
    public function paymentSucceed($id)
    {
        $purchase = $this->purchaseService->find($id);
        if (!$purchase 
            || !in_array($purchase, $this->getUser()->getPurchases()->getValues())
            || $purchase->getStatus() !== Purchase::STATUS_PENDING) {
            $this->addFlash("danger", "Vous ne possédez aucune commande en cours avec ce numéro");
            return $this->redirectToRoute("cart_show");
        }

        $event = new PurchasePaymentSucceedEvent($purchase);
        $this->dispatcher->dispatch($event, Purchase::PAYMENT_SUCCEED_EVENT);
        
        return $this->render("purchase/payment_succeed.html.twig", [
            'purchase' => $purchase,
            'user' => $this->getUser()
        ]);
    }


    /**
     * @Route("/purchase/cancel/{id}", name="purchase_cancel")
     * @IsGranted("ROLE_USER")
     */
    public function cancel($id)
    {
        $purchase = $this->purchaseService->find($id);
        if (!$purchase 
            || !in_array($purchase, $this->getUser()->getPurchases()->getValues())
            || $purchase->getStatus() !== Purchase::STATUS_PENDING) {
            $this->addFlash("danger", "Vous ne possédez aucune commande en cours avec ce numéro");
            return $this->redirectToRoute("purchase_index");
        }

        $this->purchaseService->cancel($purchase);
        $this->addFlash("success", "Votre commande a correctement été annulée");

        return $this->redirectToRoute("purchase_index");
    }

    /**
     * @Route("/administration/purchases", name="purchase_index_admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAdmin()
    {
        $purchases = $this->purchaseService->findAllCurrent();

        return $this->render('administration/purchase/current-listing.html.twig', [
            'purchases' => $purchases
        ]);
    }


    /**
     * @Route("/administration/purchases/{id}", name="purchase_show_admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAdmin($id): Response
    {
        $purchase = $this->purchaseService->find($id);

        if (!$purchase) {
                $this->addFlash('danger', 'Il n\'existe pas de commande avec cet identifiant');
                return $this->redirectToRoute("purchase_index_admin");
        }


        return $this->render('administration/purchase/show.html.twig', [
            'purchase' => $purchase
        ]);
    }

    /**
     * @Route("/administration/purchase/sent/{id}", name="purchase_sent")
     * @IsGranted("ROLE_ADMIN")
     */
    public function sent($id): Response
    {
        $purchase = $this->purchaseService->find($id);

        if (!$purchase) {
                $this->addFlash('danger', 'Il n\'existe pas de commande avec cet identifiant');
                return $this->redirectToRoute("purchase_index_admin");
        }

        $event = new PurchaseSentEvent($purchase);
        $this->dispatcher->dispatch($event, Purchase::PURCHASE_SENT_EVENT);
        $this->addFlash("success", "La commande a correctement été marquée expédiée !");

        return $this->redirectToRoute("purchase_index_admin");
    }
}
