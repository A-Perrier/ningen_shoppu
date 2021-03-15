<?php
namespace App\Subscriber\User;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use App\Event\User\EmailModifyEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailModifySubscriber implements EventSubscriberInterface
{
  private $em;
  private $mailer;

  public function __construct(EntityManagerInterface $em, MailerInterface $mailer)
  {
    $this->em = $em;
    $this->mailer = $mailer;
  }

  public static function getSubscribedEvents()
  {
    return [
      User::EMAIL_MODIFY_EVENT => [
        ["flushNewEmail", 10],
        ["sendEmail", 9]
      ]
    ];
  }


  public function flushNewEmail()
  {
    $this->em->flush();
  }

  public function sendEmail(EmailModifyEvent $event)
  {
    $user = $event->getUser();

    $email = new TemplatedEmail();
    $email->from(new Address("ningenshoppu@no-reply.com"))
          ->to(new Address($user->getUsername()))
          ->subject("NingenShoppu - Modification de votre email")
          ->htmlTemplate('emails/email-modify.html.twig')
          ->context([
            'user' => $user,
          ])
    ;

    $this->mailer->send($email);
  }
}