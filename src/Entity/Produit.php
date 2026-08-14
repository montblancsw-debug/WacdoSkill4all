<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    // Identifiant unique du produit.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le nom du produit est obligatoire.
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom du produit est obligatoire.',
        normalizer: 'trim'
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom du produit doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du produit ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $produit_nom = null;

    // La description du produit reste facultative.
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $produit_description = null;

    // Le prix du produit est obligatoire et doit être positif.
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le prix du produit est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le prix du produit doit être supérieur à 0 €.'
    )]
    private ?string $produit_prix = null;

    // Le stock doit toujours être défini.
    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La disponibilité du produit doit être renseignée.'
    )]
    private ?bool $produit_stock = null;

    // L'image reste facultative mais limitée à 255 caractères.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l\'image ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $produit_image = null;

    // La catégorie reste facultative.
    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(
        targetEntity: Detail::class,
        mappedBy: 'produit'
    )]
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
            if ($detail->getProduit() === $this) {
                $detail->setProduit(null);
            }
        }

        return $this;
    }
}