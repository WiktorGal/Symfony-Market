<?php

namespace App\Entity;

use App\Entity\Category;
use App\Entity\User; 
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[Vich\Uploadable]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'boolean')]
    private $isSold;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageName;

    /**
     * @Vich\UploadableField(mapping="item_images", fileNameProperty="imageName")
     * @Assert\File(maxSize="2M")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private $createdBy;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getPrice(): ?string
    {
        return $this->price;
    }
    public function getIsSold(): bool
    {
        return $this->isSold;
    }
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }





    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setIsSold(bool $isSold): void
    {
        $this->isSold = $isSold;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }
/**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload(): void
    {
        if ($this->imageFile instanceof UploadedFile) {
            $this->imageName = md5(uniqid()) . '.' . $this->imageFile->getClientOriginalExtension();
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function uploadImage(): void
    {
        if ($this->imageFile instanceof UploadedFile) {
            $this->imageFile->move('C:\Users\wgala\Symfony\Market\marketplace\Images', $this->imageName);
            $this->imageFile = null; // Cleanup
        }
    }
}
