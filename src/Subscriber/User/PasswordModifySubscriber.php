<?php
namespace App\Subscriber\User;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use App\Event\User\PasswordModifyEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordModifySubscriber implements EventSubscriberInterface
{
  private $security;
  private $encoder;
  private $em;
  private $mailer;

  public function __construct(Security $security, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, MailerInterface $mailer)
  {
    $this->security = $security;
    $this->encoder = $encoder;
    $this->em = $em;
    $this->mailer = $mailer;
  }

  public static function getSubscribedEvents()
  {
    return [
      User::PASSWORD_MODIFY_EVENT => [
        ['setNewPassword', 2],
        ['sendEmail', 1]
      ]
    ];
  }

  public function setNewPassword(PasswordModifyEvent $event)
  {
    $plainPassword = $event->getPlainPassword();

    /** @var User */
    $user = $this->security->getUser();
    $user->setPassword($this->encoder->encodePassword($user, $plainPassword));

    $this->em->flush();
  }

  public function sendEmail()
  {
    $user = $this->security->getUser();

    $email = new TemplatedEmail();
    $email->from(new Address("ningenshoppu@no-reply.com"))
          ->to(new Address($user->getUsername()))
          ->subject("NingenShoppu - Modification de votre mot de passe")
          ->htmlTemplate('emails/password-modify.html.twig')
          ->context([
            'user' => $user,
          ])
    ;

    $this->mailer->send($email);
  }
}