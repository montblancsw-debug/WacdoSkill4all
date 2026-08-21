<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[Vich\Uploadable]
class Menu
{
// ====================================================== //
// ===================== PROPRIETES. ==================== //
// ====================================================== //

// Identifiant unique du menu.

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

// Nom du menu.

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du menu est obligatoire.', normalizer: 'trim')]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $menu_nom = null;

// Description du menu.

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $menu_description = null;

// Prix du menu.

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix du menu est obligatoire.')]
    #[Assert\Positive(message: 'Le prix du menu doit être supérieur à 0 €.')]
    private ?string $menu_prix = null;

// Indique si le menu est actif.

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le statut du menu doit être renseigné.')]
    private ?bool $menu_status = true;

// Nom du fichier image enregistré.

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $menu_image = null;

// Fichier image envoyé depuis le formulaire.

    #[Vich\UploadableField(mapping: 'menus', fileNameProperty: 'menuImage')]
    #[Assert\Image(maxSize: '2M',mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],mimeTypesMessage: 'Veuillez choisir une image au format JPG, PNG ou WEBP.')]
    private ?File $imageFile = null;

// Date de la dernière modification de l'image.

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

// Produits composant le menu.

    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'menus')]
    #[Assert\Count(min: 1, minMessage: 'Un menu doit contenir au moins un produit.')]
    private Collection $produits;

// Lignes de commande contenant ce menu.

    #[ORM\OneToMany(targetEntity: Detail::class, mappedBy: 'menu')]
    private Collection $details;

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise les collections des produits et des détails.

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->details = new ArrayCollection();
    }

// ====================================================== //
// ================== GETTERS/SETTERS. ================== //
// ====================================================== //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuNom(): ?string
    {
        return $this->menu_nom;
    }

    public function setMenuNom(string $menu_nom): static
    {
        $this->menu_nom = $menu_nom;
        return $this;
    }

    public function getMenuDescription(): ?string
    {
        return $this->menu_description;
    }

    public function setMenuDescription(?string $menu_description): static
    {
        $this->menu_description = $menu_description;
        return $this;
    }

    public function getMenuPrix(): ?string
    {
        return $this->menu_prix;
    }

    public function setMenuPrix(?string $menu_prix): static
    {
        $this->menu_prix = $menu_prix;
        return $this;
    }

    public function isMenuStatus(): ?bool
    {
        return $this->menu_status;
    }

    public function setMenuStatus(bool $menu_status): static
    {
        $this->menu_status = $menu_status;
        return $this;
    }

    public function getMenuImage(): ?string
    {
        return $this->menu_image;
    }

    public function setMenuImage(?string $menu_image): static
    {
        $this->menu_image = $menu_image;
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

// Ajoute un produit au menu.

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
        }
        return $this;
    }

// Retire un produit du menu.

    public function removeProduit(Produit $produit): static
    {
        $this->produits->removeElement($produit);
        return $this;
    }

    public function getDetails(): Collection
    {
        return $this->details;
    }

// Ajoute une ligne de commande contenant ce menu.

    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setMenu($this);
        }
        return $this;
    }

// Retire une ligne de commande liée à ce menu.

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail) && $detail->getMenu() === $this) {
            $detail->setMenu(null);
        }
        return $this;
    }
}