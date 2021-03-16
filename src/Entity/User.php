<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte avec cette adresse email")
 */
class User implements UserInterface
{
    public const PASSWORD_MODIFY_EVENT = "user.password_modify";
    public const EMAIL_MODIFY_EVENT = "user.email_modify";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Vous devez inscrire une adresse email")
     * @Assert\Email(message="Le format de l'adrese email {{ value }} ne convient pas")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @Groups({"feedback"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez indiquer votre prénom")
     */
    private $firstName;

    /**
     * @Groups({"feedback"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez indiquer votre nom de famille")
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="user")
     */
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity=Feedback::class, mappedBy="user")
     */
    private $feedback;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }


    /**
     * Checks all the current user purchases to find if he bought a product (Purchase::STATUS_PAID || Purchase::STATUS_SENT)
     *
     * @param Product $product
     * @return boolean
     */
    public function hasBought(Product $product): bool
    {
        $hasBought = false;

        foreach ($this->getPurchases()->getValues() as $purchase) {
            foreach ($purchase->getPurchaseItems()->getValues() as $purchaseItem) {
                if ($purchaseItem->getProduct() === $product) {
                    if ($purchase->getStatus() !== Purchase::STATUS_CANCELLED 
                     && $purchase->getStatus() !== Purchase::STATUS_PENDING) {
                         $hasBought = true;
                    }
                }
            }
        }

        return $hasBought;
    }

    public function hasAlreadyFeedbacked(Product $product): bool
    {
        foreach ($this->getFeedback()->getValues() as $feedback) {
            if ($feedback->getProduct() === $product) return true;
        }

        return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setUser($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Feedback[]
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback[] = $feedback;
            $feedback->setUser($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
            }
        }

        return $this;
    }
}
