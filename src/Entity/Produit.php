<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $produit_nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $produit_description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $produit_prix = null;

    #[ORM\Column]
    private ?bool $produit_stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $produit_image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(targetEntity: Detail::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitNom(): ?string
    {
        return $this->produit_nom;
    }

    public function setProduitNom(string $produit_nom): static
    {
        $this->produit_nom = $produit_nom;

        return $this;
    }

    public function getProduitDescription(): ?string
    {
        return $this->produit_description;
    }

    public function setProduitDescription(?string $produit_description): static
    {
        $this->produit_description = $produit_description;

        return $this;
    }

    public function getProduitPrix(): ?string
    {
        return $this->produit_prix;
    }

    public function setProduitPrix(string $produit_prix): static
    {
        $this->produit_prix = $produit_prix;

        return $this;
    }

    public function isProduitStock(): ?bool
    {
        return $this->produit_stock;
    }

    public function setProduitStock(bool $produit_stock): static
    {
        $this->produit_stock = $produit_stock;

        return $this;
    }

    public function getProduitImage(): ?string
    {
        return $this->produit_image;
    }

    public function setProduitImage(?string $produit_image): static
    {
        $this->produit_image = $produit_image;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Detail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setProduit($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getProduit() === $this) {
                $detail->setProduit(null);
            }
        }

        return $this;
    }
}
