<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Image\ImagePathGenerator;
use App\Twig\ImageExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
      * @Route("/sitemap.{_format}", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request, ImagePathGenerator $imagePathGenerator, ProductRepository $productRepository, ImageExtension $imageExtension): Response
    {
        // Récupérer le nom d'hôte
        $hostname = $request->getSchemeAndHttpHost();
        
        // On initialise un tableau pour lister les URL
        $urls = [];
        
        // On ajoute les URLs statiques
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('products_all')];
        $urls[] = ['loc' => $this->generateUrl('categories_all')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('app_register')];
        $urls[] = ['loc' => $this->generateUrl('contact')];
        
        // On ajoute les URLs dynamiques
        foreach($productRepository->findAll() as $product){
            if(count($product->getProductImages()) > 0){
                $images = [
                    'loc' => $imagePathGenerator->generate($imageExtension->findFirstImageName($product), 300, 200),
                    'title' => $product->getWording()
                ];
            } else {
                $images = '';
            }
            
            
            $urls[] = [
                'loc' => $this->generateUrl('product_show', ['id' => $product->getId(), 'slug' => $product->getSlug()]),
                'image' => $images,
                // 'lastmod' => $product->getCreatedAt()->format('Y-m-d')
            ];
        }

        // Fabriquer la réponse
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname
                ]),
                200
            );
            
            
            // Ajout des entêtes
            $response->headers->set('Content-Type', 'text/xml');
            
            // Envoyer la réponse
            return $response;
        }
        
    }
    