<?php
namespace App\Event\Feedback;

use App\Entity\Feedback;
use Symfony\Contracts\EventDispatcher\Event;

class FeedbackSentEvent extends Event
{
  protected $feedback;

  public function __construct(Feedback $feedback)
  {
    $this->feedback = $feedback;
  }

  public function getFeedback()
  {
    return $this->feedback;
  }
}