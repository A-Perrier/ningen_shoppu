<?php
namespace App\Service;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeService 
{
  private $publicKey;
  private $secretKey;

  public function __construct($publicKey, $secretKey)
  {
    $this->publicKey = $publicKey;
    $this->secretKey = $secretKey;
  }

  public function getPaymentIntent(Purchase $purchase)
  {
    Stripe::setApiKey($this->secretKey);

    return PaymentIntent::create([
      'amount' => $purchase->getTotal(),
      'currency' => 'eur'
    ]);
  }
}