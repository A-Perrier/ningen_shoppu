<?php
namespace App\Service;

use App\Entity\Label;
use App\Repository\LabelRepository;
use Doctrine\ORM\EntityManagerInterface;

class LabelService
{
  private $labelRepository;
  private $em;

  public function __construct(LabelRepository $labelRepository, EntityManagerInterface $em)
  {
    $this->labelRepository = $labelRepository;
    $this->em = $em;
  }

  public function find($id)
  {
    return $this->labelRepository->find($id);
  }

  public function findAll()
  {
    return $this->labelRepository->findAll();
  }


  /**
   * On edition, we set editMode to true to avoid manager to persist
   *
   * @param Label $label
   * @param boolean $editMode
   * @return void
   */
  public function create(Label $label, $editMode = false)
  {
    if (!$editMode) $this->em->persist($label);
    $this->em->flush();
  }


  public function delete(Label $label) 
  {
    $this->em->remove($label);
    $this->em->flush();
  }
}