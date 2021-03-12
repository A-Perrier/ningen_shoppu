<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DeliveryCreateEvent extends Event
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * Returns an array of Product names / quantity which needs to be transformed into a full delivery
   */
  public function getData()
  {
    return $this->data;
  }
}