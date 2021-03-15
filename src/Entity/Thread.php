<?php

namespace App\Entity;

use App\Repository\ThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThreadRepository::class)
 */
class Thread
{

    public const STATUS_CLOSED = "CLOSE";
    public const STATUS_OPEN = "OPEN";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=ContactEmail::class, mappedBy="thread")
     */
    private $emails;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startedAt;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastEmail()
    {
        $emails = $this->getEmails()->getValues();
        return end($emails);
    }

    /**
     * @return Collection|ContactEmail[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(ContactEmail $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
            $email->setThread($this);
        }

        return $this;
    }

    public function removeEmail(ContactEmail $email): self
    {
        if ($this->emails->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getThread() === $this) {
                $email->setThread(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }
}
