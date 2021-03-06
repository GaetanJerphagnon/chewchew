<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\HttpFoundation\File\File;
/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Vich\Uploadable
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @Vich\UploadableField(mapping="product_pictures", fileNameProperty="picture")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="products")
     */
    private $restaurant;

    /**
     * @ORM\ManyToMany(targetEntity=Menu::class, mappedBy="products")
     */
    private $menus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="products")
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=OrderHasProducts::class, mappedBy="products", cascade={"persist", "remove"})
     */
    private $productHasOrders;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $this->productHasOrders = new ArrayCollection();

    }

    public function __toString()
    {
        return $this->name. " - ". $this->price/100 ." ???";
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function setImageFile(File $file = null)
    {
        $this->imageFile = $file;

        if($file) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->addProduct($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeProduct($this);
        }

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

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

    /**
     * @return Collection|OrderHasProducts[]
     */
    public function getProductHasOrders(): Collection
    {
        return $this->productHasOrders;
    }

    public function addProductHasOrder(OrderHasProducts $productHasOrder): self
    {
        if (!$this->productHasOrders->contains($productHasOrder)) {
            $this->productHasOrders[] = $productHasOrder;
            $productHasOrder->setProducts($this);
        }

        return $this;
    }

    public function removeProductHasOrder(OrderHasProducts $productHasOrder): self
    {
        if ($this->productHasOrders->removeElement($productHasOrder)) {
            // set the owning side to null (unless already changed)
            if ($productHasOrder->getProducts() === $this) {
                $productHasOrder->setProducts(null);
            }
        }

        return $this;
    }
}
