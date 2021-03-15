<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Entity\ContactEmail;
use App\Event\Contact\ContactFormSentEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request): Response
    {
        $email = new ContactEmail();

        $form = $this->createForm(ContactType::class, $email);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new ContactFormSentEvent($email), ContactEmail::CONTACT_FORM_SENT_EVENT);
            $this->addFlash("success", "Votre email a correctement été envoyé ! L'équipe se charge de vous y répondre au plus vite");

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
