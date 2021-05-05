<?php

namespace App\Entity;

use App\Repository\OrderHasProductsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderHasProductsRepository::class)
 */
class OrderHasProducts
{
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     */
    private $quantity;
    
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderHasProducts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id",nullable=true)
     */
    private $orders;
    
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productHasOrders", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id",nullable=true)
     */
    private $products;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct(Product $product = null, int $quantity = null)
    {
        $this->products = $product;
        $this->quantity = $quantity;
    }

    public function __toString()
    {
        return $this->quantity.' x '.$this->products->getName();
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
