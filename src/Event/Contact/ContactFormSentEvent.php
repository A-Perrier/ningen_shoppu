<?php
namespace App\Event\Contact;

use App\Entity\ContactEmail;
use Symfony\Contracts\EventDispatcher\Event;

class ContactFormSentEvent extends Event
{
  protected $email;

  public function __construct(ContactEmail $email)
  {
    $this->email = $email;
  }

  public function getEmail()
  {
    return $this->email;
  }
}