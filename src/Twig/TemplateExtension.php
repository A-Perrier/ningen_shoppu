<?php
namespace App\Twig;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateExtension extends AbstractExtension
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function getFunctions()
  {
    return [
      new TwigFunction('template', [$this, 'findTemplate'])
    ];
  }

  public function findTemplate()
  {
    $roles = [];

    if ($this->security->getUser()) {
      $roles = $this->security->getUser()->getRoles();
    }
    
    return in_array("ROLE_ADMIN", $roles) ? "admin.html.twig" : "base.html.twig";
  }
}