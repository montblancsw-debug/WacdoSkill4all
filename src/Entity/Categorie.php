<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le nom de la catégorie est obligatoire.
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom de la catégorie est obligatoire.',
        normalizer: 'trim'
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom de la catégorie doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $categorie_nom = null;

    // La description de la catégorie reste facultative.
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $categorie_description = null;

    // Le nom du fichier est facultatif mais limité à 255 caractères.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du fichier ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $categorie_nomdefichier = null;

    // Le nom de l'image est facultatif mais limité à 255 caractères.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l\'image ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $categorie_image = null;

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

    public function setCategorieDescription(?string $categorie_description): static
    {
        $this->categorie_description = $categorie_description;
        return $this;
    }

    public function getCategorieNomdefichier(): ?string
    {
        return $this->categorie_nomdefichier;
    }

    public function setCategorieNomdefichier(?string $categorie_nomdefichier): static
    {
        $this->categorie_nomdefichier = $categorie_nomdefichier;
        return $this;
    }

    public function getCategorieImage(): ?string
    {
        return $this->categorie_image;
    }

    public function setCategorieImage(?string $categorie_image): static
    {
        $this->categorie_image = $categorie_image;
        return $this;
    }

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
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }
        return $this;
    }
}
