<?php
namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class EmailModifyEvent extends Event
{
  protected $user;

  public function __construct(User $user)
  {
    $this->user = $user;
  }

  public function getUser()
  {
    return $this->user;
  }
}