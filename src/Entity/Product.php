<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @Groups("delivery")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("delivery")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le produit doit avoir un libellé")
     * @Assert\Length(min="2", minMessage="Le libellé doit avoir au moins 2 caractères")
     */
    private $wording;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le produit doit posséder une description")
     * @Assert\Length(min="5", minMessage="La description doit faire au moins 5 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $rating = [];

    /**
     * @MaxDepth(2)
     * @Groups("delivery")
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotBlank(message="Votre produit doit avoir une catégorie")
     */
    private $category;

    /**
     * @Groups("delivery")
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="Le prix doit être présent")
     * @Assert\Positive(message="Le prix doit être supérieur à 0")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=ProductImage::class, mappedBy="product")
     */
    private $productImages;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOnSale;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="product")
     */
    private $purchaseItems;

    /**
     * @Groups("delivery")
     * @ORM\Column(type="integer")
     */
    private $quantityInStock;

    /**
     * @ORM\OneToMany(targetEntity=DeliveryItem::class, mappedBy="product", orphanRemoval=true)
     */
    private $deliveryItems;

    public function __construct()
    {
        $this->productImages = new ArrayCollection();
        $this->purchaseItems = new ArrayCollection();
        $this->deliveryItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRating(): ?array
    {
        return $this->rating;
    }

    public function setRating(?array $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ProductImage[]
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImage $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages[] = $productImage;
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImage $productImage): self
    {
        if ($this->productImages->removeElement($productImage)) {
            // set the owning side to null (unless already changed)
            if ($productImage->getProduct() === $this) {
                $productImage->setProduct(null);
            }
        }

        return $this;
    }

    public function getIsOnSale(): ?bool
    {
        return $this->isOnSale;
    }

    public function setIsOnSale(bool $isOnSale): self
    {
        $this->isOnSale = $isOnSale;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getProduct() === $this) {
                $purchaseItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getQuantityInStock(): ?int
    {
        return $this->quantityInStock;
    }

    public function setQuantityInStock(int $quantityInStock): self
    {
        $this->quantityInStock = $quantityInStock;

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
            $deliveryItem->setProduct($this);
        }

        return $this;
    }

    public function removeDeliveryItem(DeliveryItem $deliveryItem): self
    {
        if ($this->deliveryItems->removeElement($deliveryItem)) {
            // set the owning side to null (unless already changed)
            if ($deliveryItem->getProduct() === $this) {
                $deliveryItem->setProduct(null);
            }
        }

        return $this;
    }
}
