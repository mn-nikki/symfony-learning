<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptPart::class, mappedBy="ingredient")
     */
    private $receiptParts;

    public function __construct()
    {
        $this->receiptParts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
     * @return Collection|ReceiptPart[]
     */
    public function getReceiptParts(): Collection
    {
        return $this->receiptParts;
    }

    public function addReceiptPart(ReceiptPart $receiptPart): self
    {
        if (!$this->receiptParts->contains($receiptPart)) {
            $this->receiptParts[] = $receiptPart;
            $receiptPart->setIngredient($this);
        }

        return $this;
    }

    public function removeReceiptPart(ReceiptPart $receiptPart): self
    {
        if ($this->receiptParts->contains($receiptPart)) {
            $this->receiptParts->removeElement($receiptPart);
            // set the owning side to null (unless already changed)
            if ($receiptPart->getIngredient() === $this) {
                $receiptPart->setIngredient(null);
            }
        }

        return $this;
    }
}
