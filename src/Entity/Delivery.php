<?php

namespace App\Entity;

use App\Repository\DeliveryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryRepository::class)
 */
class Delivery
{
    public const DELIVERY_CREATE_EVENT = 'delivery.create';


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $carrier;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliveredAt;

    /**
     * @ORM\OneToMany(targetEntity=DeliveryItem::class, mappedBy="delivery", orphanRemoval=true)
     */
    private $deliveryItems;

    public function __construct()
    {
        $this->deliveryItems = new ArrayCollection();
    }

    public function getTotalDistinct()
    {
        return count($this->getDeliveryItems()->getValues());
    }

    public function getTotalUVC()
    {
        $total = 0;

        foreach($this->getDeliveryItems()->getValues() as $deliveryItem) {
            $total += $deliveryItem->getQuantity();
        }

        return $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    public function setCarrier(string $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getDeliveredAt(): ?\DateTimeInterface
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(\DateTimeInterface $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * @return Collection|DeliveryItem[]
     */
    public function getDeliveryItems(): Collection
    {
        return $this->deliveryItems;
    }

    public function addDeliveryItem(DeliveryItem $deliveryItem): self
    {
        if (!$this->deliveryItems->contains($deliveryItem)) {
            $this->deliveryItems[] = $deliveryItem;
            $deliveryItem->setDelivery($this);
        }

        return $this;
    }

    public function removeDeliveryItem(DeliveryItem $deliveryItem): self
    {
        if ($this->deliveryItems->removeElement($deliveryItem)) {
            // set the owning side to null (unless already changed)
            if ($deliveryItem->getDelivery() === $this) {
                $deliveryItem->setDelivery(null);
            }
        }

        return $this;
    }
}
