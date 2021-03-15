<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedbackRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
 * @ORM\Entity(repositoryClass=FeedbackRepository::class)
 */
class Feedback
{

    public const FEEDBACK_SENT_EVENT = 'feedback.sent';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"feedback"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="feedback")
     */
    private $user;

    /**
     * @Groups({"feedback"})
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous ne pouvez pas laisser ce champ vide")
     * @Assert\Length(min="2", minMessage="Votre commentaire doit faire au moins 2 caractères")
     */
    private $comment;

    /**
     * @Groups({"feedback"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Vous ne pouvez pas ne pas mettre de note")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(5)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="feedback")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
