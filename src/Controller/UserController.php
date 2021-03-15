<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Form\EmailModifyType;
use App\Form\PasswordModifyType;
use App\Event\User\EmailModifyEvent;
use App\Event\User\PasswordModifyEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userService;
    private $dispatcher;

    public function __construct(UserService $userService, EventDispatcherInterface $dispatcher)
    {
        $this->userService = $userService;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @Route("/user", name="user_home")
     * @IsGranted("ROLE_USER")
     */
    public function home(): Response
    {
        $user = $this->userService->getUserOrThrow("Accès refusé");

        return $this->render('user/home.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("/password_modify", name="user_password_modify")
     * @IsGranted("ROLE_USER")
     */
    public function password_modify(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordModifyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new PasswordModifyEvent($form->getData()["password"]), User::PASSWORD_MODIFY_EVENT);
            $this->addFlash("success", "Votre mot de passe a correctement été modifié. Un email de confirmation vous a été envoyé");
        }

        return $this->render("user/password-modify.html.twig", [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/email_modify", name="user_email_modify")
     * @IsGranted("ROLE_USER")
     */
    public function email_modify(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EmailModifyType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new EmailModifyEvent($form->getData()), User::EMAIL_MODIFY_EVENT);
            $this->addFlash("success", "Félicitations ! Votre adresse a correctement été modifiée. Un email de confirmation vous a été envoyé");
        }

        return $this->render("user/email-modify.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
