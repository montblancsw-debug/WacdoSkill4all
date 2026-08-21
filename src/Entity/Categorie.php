<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[Vich\Uploadable]
class Categorie
{
// ====================================================== //
// ===================== PROPRIETES. ==================== //
// ====================================================== //

// Identifiant unique de la catégorie.

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

// Nom de la catégorie.

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la catégorie est obligatoire.',normalizer: 'trim')]
    #[Assert\Length(min: 2,max: 255,minMessage: 'Le nom de la catégorie doit contenir au moins {{ limit }} caractères.',maxMessage: 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $categorie_nom = null;

// Description de la catégorie.

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $categorie_description = null;

// Nom du fichier image enregistré.

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255,maxMessage: 'Le nom du fichier ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $categorie_nomdefichier = null;

// Nom de l'image de la catégorie.

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255,maxMessage: 'Le nom de l\'image ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $categorie_image = null;

// Fichier image envoyé depuis le formulaire.

    #[Vich\UploadableField(mapping: 'categories',fileNameProperty: 'categorieNomdefichier')]
    #[Assert\Image(maxSize: '2M',mimeTypes: ['image/jpeg','image/png','image/webp'],mimeTypesMessage: 'Veuillez choisir une image au format JPG, PNG ou WEBP.')]
    private ?File $imageFile = null;

// Date de la dernière modification de l'image.

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

// Produits appartenant à cette catégorie.

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'categorie')]
    private Collection $produits;

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise la collection des produits.

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

// ====================================================== //
// ================== GETTERS/SETTERS. ================== //
// ====================================================== //

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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

// Met à jour la date lorsqu'une nouvelle image est envoyée.

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getProduits(): Collection
    {
        return $this->produits;
    }

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

// Ajoute un produit à la catégorie.

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setCategorie($this);
        }
        return $this;
    }

// Retire un produit de la catégorie.

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