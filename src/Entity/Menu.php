<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $menu_nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $menu_description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $menu_prix = null;

    #[ORM\Column]
    private ?bool $menu_status = null;

    #[ORM\Column(length: 255)]
    private ?string $menu_image = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'menus')]
    private Collection $produits;

    /**
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(targetEntity: Detail::class, mappedBy: 'menu')]
    private Collection $details;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->details = new ArrayCollection();
    }

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

    public function setMenuPrix(string $menu_prix): static
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

    public function setMenuImage(string $menu_image): static
    {
        $this->menu_image = $menu_image;

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
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        $this->produits->removeElement($produit);

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
            $detail->setMenu($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getMenu() === $this) {
                $detail->setMenu(null);
            }
        }

        return $this;
    }
}
