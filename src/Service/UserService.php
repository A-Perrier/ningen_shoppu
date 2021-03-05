<?php
namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class UserService
{
  private $security;
  private $userRepository;

  public function __construct(Security $security, UserRepository $userRepository)
  {
    $this->security = $security;
    $this->userRepository = $userRepository;
  }

  public function getUser()
  {
    return $this->security->getUser();
  }

  public function getUserOrThrow(?string $message = null)
  {
    $user = $this->getUser();
    
    if (!$user) throw new AccessDeniedException($message);

    return $user;
  }
}