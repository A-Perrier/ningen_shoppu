<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
}
