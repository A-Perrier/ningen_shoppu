<?php
namespace App\Subscriber\Feedback;

use App\Entity\Feedback;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedbackSentSubscriber implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    return [
      Feedback::FEEDBACK_SENT_EVENT => [
        ['methodName', 10]
      ]
    ];
  }
}