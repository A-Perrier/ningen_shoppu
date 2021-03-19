<?php
namespace App\Controller;

use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Glide\Signatures\SignatureException;
use Symfony\Component\Routing\Annotation\Route;
use League\Glide\Responses\SymfonyResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImageController extends AbstractController
{

  protected $glideKey;

  public function __construct(ParameterBagInterface $parameterBagInterface)
  {
    $this->glideKey = $parameterBagInterface->get('glide_key');
  }

  /**
   * @Route("/images/{path}")
   */
  public function show(Request $request, $path, ParameterBagInterface $parameterBagInterface): Response
  {
    $server = ServerFactory::create([
      'response' => new SymfonyResponseFactory($request),
      'cache' => $parameterBagInterface->get('kernel.project_dir').'/var/images',
      'source' => $parameterBagInterface->get('kernel.project_dir').'/public/img/products',
      'base_url' => 'images',
      'defaults' => [
        'q' => 80
      ]
    ]);
    [$url] = explode('?', $request->getRequestUri());

    try {
        SignatureFactory::create($this->glideKey)->validateRequest($url, $_GET);
        
        return $server->getImageResponse($path, $request->query->all());
    } catch (SignatureException $e) {
        throw new HttpException(403, "Signature invalide");
    }

  }


  /**
   * @Route("/assets/{path}")
   */
  public function showAsset(Request $request, $path, ParameterBagInterface $parameterBagInterface): Response
  {
    $server = ServerFactory::create([
      'response' => new SymfonyResponseFactory($request),
      'cache' => $parameterBagInterface->get('kernel.project_dir').'/var/assets',
      'source' => $parameterBagInterface->get('kernel.project_dir').'/public/img/icons',
      'base_url' => 'assets',
      'defaults' => [
        'q' => 50
      ]
    ]);
    [$url] = explode('?', $request->getRequestUri());

    try {
        SignatureFactory::create($this->glideKey)->validateRequest($url, $_GET);
        
        return $server->getImageResponse($path, $request->query->all());
    } catch (SignatureException $e) {
        throw new HttpException(403, "Signature invalide");
    }

  }


  /**
   * @Route("/labels/{path}")
   */
  public function showLabel(Request $request, $path, ParameterBagInterface $parameterBagInterface): Response
  {
    $server = ServerFactory::create([
      'response' => new SymfonyResponseFactory($request),
      'cache' => $parameterBagInterface->get('kernel.project_dir').'/var/labels',
      'source' => $parameterBagInterface->get('kernel.project_dir').'/public/img/labels',
      'base_url' => 'labels',
      'defaults' => [
        'q' => 50
      ]
    ]);
    [$url] = explode('?', $request->getRequestUri());

    try {
        SignatureFactory::create($this->glideKey)->validateRequest($url, $_GET);
        
        return $server->getImageResponse($path, $request->query->all());
    } catch (SignatureException $e) {
        throw new HttpException(403, "Signature invalide");
    }

  }

}