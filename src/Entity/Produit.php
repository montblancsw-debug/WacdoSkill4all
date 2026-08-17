<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
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

    // La description reste facultative.
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $produit_description = null;

    // Le prix est obligatoire et doit être positif.
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le prix du produit est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le prix du produit doit être supérieur à 0 €.'
    )]
    private ?string $produit_prix = null;

    // Indique si le produit est disponible.
    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La disponibilité du produit doit être renseignée.'
    )]
    private ?bool $produit_stock = null;

    // Contient le nom du fichier enregistré.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l\'image ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $produit_image = null;

    // Reçoit temporairement le fichier envoyé.
    #[Vich\UploadableField(
        mapping: 'produits',
        fileNameProperty: 'produitImage'
    )]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/webp'
        ],
        mimeTypesMessage: 'Veuillez choisir une image au format JPG, PNG ou WEBP.'
    )]
    private ?File $imageFile = null;

    // Permet à Doctrine de détecter un changement d'image.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // La catégorie peut être supprimée sans supprimer le produit.
    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(
        targetEntity: Detail::class,
        mappedBy: 'produit'
    )]
    private Collection $details;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'produits')]
    private Collection $menus;

    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->menus = new ArrayCollection();
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

    public function setProduitPrix(?string $produit_prix): static
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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

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

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->addProduit($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeProduit($this);
        }

        return $this;
    }
}