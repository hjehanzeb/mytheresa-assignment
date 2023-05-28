<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, name: 'discount_type')]
    private ?string $discountType = null;

    #[ORM\Column(name: 'discount_percent')]
    private ?int $discountPercent = null;

    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\OneToOne(inversedBy: 'discount', cascade: ['persist', 'remove'])]
    private ?ProductCategory $productCategory = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productSKU = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $discountType): self
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(int $discountPercent): self
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setDiscount($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getDiscount() === $this) {
                $product->setDiscount(null);
            }
        }

        return $this;
    }

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getProductSKU(): ?string
    {
        return $this->productSKU;
    }

    public function setProductSKU(?string $productSKU): self
    {
        $this->productSKU = $productSKU;

        return $this;
    }
}
