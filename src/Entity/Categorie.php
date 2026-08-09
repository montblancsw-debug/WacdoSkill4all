<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie_nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $categorie_description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie_nomdefichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie_image = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'categorie')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorieNom(): ?string
    {
        return $this->categorie_nom;
    }

    public function setCategorieNom(string $categorie_nom): static
    {
        $this->categorie_nom = $categorie_nom;

        return $this;
    }

    public function getCategorieDescription(): ?string
    {
        return $this->categorie_description;
    }

    public function setCategorieDescription(string $categorie_description): static
    {
        $this->categorie_description = $categorie_description;

        return $this;
    }

    public function getCategorieNomdefichier(): ?string
    {
        return $this->categorie_nomdefichier;
    }

    public function setCategorieNomdefichier(string $categorie_nomdefichier): static
    {
        $this->categorie_nomdefichier = $categorie_nomdefichier;

        return $this;
    }

    public function getCategorieImage(): ?string
    {
        return $this->categorie_image;
    }

    public function setCategorieImage(string $categorie_image): static
    {
        $this->categorie_image = $categorie_image;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }
}
