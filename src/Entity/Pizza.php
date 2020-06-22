<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PizzaRepository::class)
 */
class Pizza
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $diameter;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptPart::class, mappedBy="pizza", cascade={"persist"}, fetch="EAGER")
     */
    private Collection $parts;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
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

    public function getDiameter(): ?int
    {
        return $this->diameter;
    }

    public function setDiameter(int $diameter): self
    {
        $this->diameter = $diameter;

        return $this;
    }

    /**
     * @return Collection|ReceiptPart[]
     */
    public function getParts(): Collection
    {
        return $this->parts;
    }

    public function addPart(ReceiptPart $part): self
    {
        if (!$this->parts->contains($part)) {
            $this->parts[] = $part;
            $part->setPizza($this);
        }

        return $this;
    }

    public function removePart(ReceiptPart $part): self
    {
        if ($this->parts->contains($part)) {
            $this->parts->removeElement($part);
            // set the owning side to null (unless already changed)
            if ($part->getPizza() === $this) {
                $part->setPizza(null);
            }
        }

        return $this;
    }
}
