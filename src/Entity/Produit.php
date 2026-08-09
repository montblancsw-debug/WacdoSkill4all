<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*
 * Entité Produit du projet Wacdo.
 *
 * Cahier des charges :
 * cette entité contient les articles proposés à la vente
 * sur la borne de commande.
 *
 * Un produit possède :
 * - un nom ;
 * - une description ;
 * - un prix ;
 * - un état de disponibilité ;
 * - une image ;
 * - éventuellement une catégorie.
 */
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    /*
     * Identifiant unique du produit.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /*
     * Nom du produit.
     *
     * Exemple :
     * WacFat, Tommeawac, Wacoco...
     */
    #[ORM\Column(length: 255)]
    private ?string $produit_nom = null;

    /*
     * Description détaillée du produit.
     *
     * Ce champ est facultatif conformément
     * au modèle de données Wacdo.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $produit_description = null;

    /*
     * Prix de vente du produit.
     *
     * DECIMAL(10,2) permet de conserver
     * correctement les prix en euros.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $produit_prix = null;

    /*
     * Indique si le produit est actuellement
     * disponible à la vente.
     */
    #[ORM\Column]
    private ?bool $produit_stock = null;

    /*
     * Image associée au produit.
     *
     * La gestion réelle de l'upload sera mise
     * en place plus tard dans le chapitre images.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $produit_image = null;

    /*
     * Relation Produit -> Categorie.
     *
     * Une catégorie permet d'organiser les produits
     * par famille : Burgers, Boissons, Desserts...
     *
     * Adaptation Wacdo :
     * si une catégorie est supprimée,
     * le produit doit être conservé.
     *
     * Sa categorie_id passe donc automatiquement à NULL.
     */
    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Categorie $categorie = null;

    /**
     * Lignes de commandes dans lesquelles
     * le produit a été utilisé.
     *
     * IMPORTANT :
     * nous n'utilisons PAS orphanRemoval ici.
     *
     * Une suppression d'un produit du catalogue
     * ne doit jamais supprimer l'historique
     * des commandes Wacdo.
     *
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(
        targetEntity: Detail::class,
        mappedBy: 'produit'
    )]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    /*
     * Retourne l'identifiant du produit.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /*
     * Retourne le nom du produit.
     */
    public function getProduitNom(): ?string
    {
        return $this->produit_nom;
    }

    /*
     * Modifie le nom du produit.
     */
    public function setProduitNom(string $produit_nom): static
    {
        $this->produit_nom = $produit_nom;

        return $this;
    }

    /*
     * Retourne la description du produit.
     */
    public function getProduitDescription(): ?string
    {
        return $this->produit_description;
    }

    /*
     * Modifie la description du produit.
     */
    public function setProduitDescription(?string $produit_description): static
    {
        $this->produit_description = $produit_description;

        return $this;
    }

    /*
     * Retourne le prix du produit.
     */
    public function getProduitPrix(): ?string
    {
        return $this->produit_prix;
    }

    /*
     * Modifie le prix du produit.
     */
    public function setProduitPrix(string $produit_prix): static
    {
        $this->produit_prix = $produit_prix;

        return $this;
    }

    /*
     * Retourne l'état du stock.
     */
    public function isProduitStock(): ?bool
    {
        return $this->produit_stock;
    }

    /*
     * Modifie l'état du stock.
     */
    public function setProduitStock(bool $produit_stock): static
    {
        $this->produit_stock = $produit_stock;

        return $this;
    }

    /*
     * Retourne l'image du produit.
     */
    public function getProduitImage(): ?string
    {
        return $this->produit_image;
    }

    /*
     * Modifie l'image du produit.
     */
    public function setProduitImage(?string $produit_image): static
    {
        $this->produit_image = $produit_image;

        return $this;
    }

    /*
     * Retourne la catégorie du produit.
     *
     * NULL est possible lorsqu'une catégorie
     * précédemment associée a été supprimée.
     */
    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    /*
     * Associe le produit à une catégorie.
     */
    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Retourne les lignes de commande
     * associées au produit.
     *
     * @return Collection<int, Detail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    /*
     * Associe une ligne de commande au produit.
     */
    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);

            $detail->setProduit($this);
        }

        return $this;
    }

    /*
     * Retire l'association entre le produit
     * et une ligne de commande.
     *
     * La ligne Detail elle-même n'est pas supprimée.
     */
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