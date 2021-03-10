<?php
namespace App\Twig;

use App\Entity\Purchase;
use PhpParser\Node\Stmt\Return_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PurchaseStatusExtension extends AbstractExtension
{

  public const STATUS_CANCELLED_COLOR = "#CD0909";
  public const STATUS_PENDING_COLOR = "#FF7C5C";
  public const STATUS_PAID_COLOR = "#33b5e5";
  public const STATUS_SENT_COLOR = "#68C068";

  public const STATUS_CANCELLED = "Annulée";
  public const STATUS_PENDING = "A payer";
  public const STATUS_PAID = "En préparation";
  public const STATUS_SENT = "Expédié";
 
  public function getFunctions()
  {
    return [
      new TwigFunction('color', [$this, 'getBackgroundColor']),
      new TwigFunction('format', [$this, 'getDetailedStatus'])
    ];
  }

  public function getBackgroundColor(string $status)
  {
    if ($status === Purchase::STATUS_CANCELLED) return self::STATUS_CANCELLED_COLOR;
    if ($status === Purchase::STATUS_PENDING) return self::STATUS_PENDING_COLOR;
    if ($status === Purchase::STATUS_PAID) return self::STATUS_PAID_COLOR;
    if ($status === Purchase::STATUS_SENT) return self::STATUS_SENT_COLOR;
  }

  public function getDetailedStatus(string $status)
  {
    if ($status === Purchase::STATUS_CANCELLED) return self::STATUS_CANCELLED;
    if ($status === Purchase::STATUS_PENDING) return self::STATUS_PENDING;
    if ($status === Purchase::STATUS_PAID) return self::STATUS_PAID;
    if ($status === Purchase::STATUS_SENT) return self::STATUS_SENT;
  }
}