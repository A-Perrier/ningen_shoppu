<?php
namespace App\Event\User;

use Symfony\Contracts\EventDispatcher\Event;

class PasswordModifyEvent extends Event
{
  private $plainPassword;

  public function __construct(string $plainPassword)
  {
    $this->plainPassword = $plainPassword;
  }

  public function getPlainPassword()
  {
    return $this->plainPassword;
  }
}