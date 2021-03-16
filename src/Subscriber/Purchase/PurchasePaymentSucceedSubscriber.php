<?php
namespace App\Subscriber\Purchase;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Service\CartService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\PurchasePaymentSucceedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchasePaymentSucceedSubscriber implements EventSubscriberInterface
{
  private $em;
  private $cartService;
  private $mailer;

  public function __construct(EntityManagerInterface $em, CartService $cartService, MailerInterface $mailer)
  {
    $this->em = $em;
    $this->cartService = $cartService;
    $this->mailer = $mailer;
  }

  public static function getSubscribedEvents()
  {
    return [
      Purchase::PAYMENT_SUCCEED_EVENT => [
        ["setPaid", 10],
        ["emptyCart", 9],
        ["decreaseStock", 8],
        ["sendEmail", 7]
      ]
    ];
  }

  public function emptyCart()
  {
    $this->cartService->empty();
  }

  public function setPaid(PurchasePaymentSucceedEvent $event)
  {
    $purchase = $event->getPurchase();
    $purchase->setStatus(Purchase::STATUS_PAID);

    $this->em->flush();
  }

  public function decreaseStock(PurchasePaymentSucceedEvent $event)
  {
    $purchase = $event->getPurchase();

    foreach ($purchase->getPurchaseItems()->getValues() as $purchaseItem) {
      /** @var Product */
      $product = $purchaseItem->getProduct();
      $quantity = $purchaseItem->getQuantity();

      $product->setQuantityInStock(($product->getQuantityInStock() - $quantity));

      if($product->getQuantityInStock() == 0) $product->setIsOnSale(false);
    }

    $this->em->flush();
  }

  public function sendEmail(PurchasePaymentSucceedEvent $event)
  {
    $purchase = $event->getPurchase();
    $user = $purchase->getUser();

    $email = new TemplatedEmail();
    $email->from(new Address("no-reply@ningenshoppu.com"))
          ->to(new Address($user->getEmail()))
          ->subject("Validation de votre commande !")
          ->htmlTemplate('emails/purchase/validation.html.twig')
          ->context([
            'purchase' => $purchase,
            'shippingFee' => Purchase::SHIPPING_FEE,
            'user' => $user
          ])
    ;

    $this->mailer->send($email);
  }
}