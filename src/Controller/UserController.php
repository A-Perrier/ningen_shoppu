<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\User\PasswordModifyEvent;
use App\Form\PasswordModifyType;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

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
}
