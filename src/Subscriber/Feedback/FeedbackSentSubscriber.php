<?php
namespace App\Subscriber\Feedback;

use App\Entity\Feedback;
use App\Event\Feedback\FeedbackSentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedbackSentSubscriber implements EventSubscriberInterface
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public static function getSubscribedEvents()
  {
    return [
      Feedback::FEEDBACK_SENT_EVENT => [
        ['save', 10]
      ]
    ];
  }

  public function save(FeedbackSentEvent $event)
  {
    $feedback = $event->getFeedback();
    $user = $feedback->getUser();
    $product = $feedback->getProduct();

    $product->addFeedback($feedback);
    $user->addFeedback($feedback);

    $this->em->persist($feedback);
    $this->em->flush();
  }
}