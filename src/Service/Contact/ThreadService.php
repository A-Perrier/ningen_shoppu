<?php
namespace App\Service\Contact;

use App\Repository\ThreadRepository;

class ThreadService
{
  private $threadRepository;

  public function __construct(ThreadRepository $threadRepository)
  {
    $this->threadRepository = $threadRepository;
  }

  public function findLast()
  {
    return $this->threadRepository->findOneBy([], ['id' => 'DESC']);
  }

}