<?php
namespace App\Controller\Api;

use App\Entity\Feedback;
use App\Event\Feedback\FeedbackSentEvent;
use Exception;
use App\Service\ProductService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeedbackController extends AbstractController
{
  private $productService;
  private $dispatcher;
  private $serializer;

  public function __construct(ProductService $productService, EventDispatcherInterface $dispatcher, SerializerInterface $serializer)
  {
    $this->productService = $productService;
    $this->dispatcher = $dispatcher;
    $this->serializer = $serializer;
  }

  /**
   * @Route("/api/product/feedback", name="api/product_feedback", methods={"POST"})
   */
  public function feedback(Request $request, ValidatorInterface $validator)
  {
    if (!$request->isXmlHttpRequest()) throw new Exception("Une erreur est survenue", 400);

    $data = json_decode($request->getContent());
    
    $product = $this->productService->find($data->productId);
    if (!$product) return $this->json("Aucun produit avec cet identifiant", 400);

    $feedback = new Feedback();
    $feedback->setComment($data->comment)
             ->setRating($data->rating)
             ->setCreatedAt(new DateTime())
             ->setProduct($product)
             ->setUser($this->getUser())
    ;

    $errors = $validator->validate($feedback);
    $parsedErrors = [];

    if (count($errors) > 0) {

        for ($i = 0; $i < count($errors); $i++) {
            $parsedErrors[$errors->get($i)->getPropertyPath()] = $errors->get($i)->getMessage();
        }
        return $this->json($parsedErrors, 400);

    } else {
        $this->dispatcher->dispatch(new FeedbackSentEvent($feedback), Feedback::FEEDBACK_SENT_EVENT);
        $jsonFeedback = $this->serializer->serialize($feedback, 'json', ['groups' => 'feedback']);

        return $this->json($jsonFeedback);
    }

    
  }
}