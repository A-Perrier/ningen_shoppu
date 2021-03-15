<?php
namespace App\Subscriber\Contact;

use DateTime;
use App\Entity\Thread;
use App\Entity\ContactEmail;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\Contact\ContactFormSentEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactFormSentSubscriber implements EventSubscriberInterface
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
      ContactEmail::CONTACT_FORM_SENT_EVENT => [
        ['process', 10]
      ]
    ];
  }


  /**
   * Launches all the methods required on CONTACT_FORM_SENT_EVENT and flushed persisted datas
   *
   * @param ContactFormSentEvent $event
   * @return void
   */
  public function process(ContactFormSentEvent $event)
  {
    $thread = $this->startThread($event);
    $email = $this->saveEmail($event, $thread);
    $this->em->flush();

    $this->sendAdmin($thread);
    $this->sendUserCopy($thread);
  }

  /**
   * Start a new thread and persists it
   *
   * @return Thread $thread
   */
  private function startThread()
  {
    $thread = new Thread();
    $thread->setStartedAt(new DateTime())
           ->setStatus(Thread::STATUS_OPEN)
    ;
    $this->em->persist($thread);

    return $thread;
  }


  /**
   * Set data to the email received from event, set it to the matching thread then persists it
   *
   * @param ContactFormSentEvent $event
   * @param Thread $thread
   * @return ContactEmail $email
   */
  private function saveEmail(ContactFormSentEvent $event, Thread $thread)
  {
    $email = $event->getEmail();
    $email->setSentAt(new DateTime())
          ->setThread($thread)
    ;

    $this->em->persist($email);
    $thread->addEmail($email);
    
    return $email;
  }


  /**
   * Send the received email to the administrators
   *
   * @param Thread $thread
   * @return void
   */
  private function sendAdmin(Thread $thread)
  {
    /** @var ContactEmail */
    $flushedEmail = $thread->getLastEmail();

    $email = new TemplatedEmail();
    $email->from(new Address($flushedEmail->getEmail()))
          ->to(new Address("contact@ningenshoppu.com"))
          ->subject($flushedEmail->getSubject())
          ->htmlTemplate('emails/administration/contact.html.twig')
          ->context([
            'flushedEmail' => $flushedEmail
          ])
    ;

    $this->mailer->send($email);
  }


  /**
   * Send a copy of the received email to the sender
   *
   * @param Thread $thread
   * @return void
   */
  private function sendUserCopy(Thread $thread)
  {
    /** @var ContactEmail */
    $flushedEmail = $thread->getLastEmail();

    $email = new TemplatedEmail();
    $email->from(new Address("contact@ningenshoppu.com"))
          ->to(new Address($flushedEmail->getEmail()))
          ->subject("NingenShoppu - Votre demande n°" . $thread->getId())
          ->htmlTemplate('emails/thread/first-email.html.twig')
          ->context([
            'flushedEmail' => $flushedEmail,
            'thread' => $thread
          ])
    ;

    $this->mailer->send($email);
  }
}