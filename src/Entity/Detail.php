<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    // Identifiant unique de la ligne de commande.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // La quantité doit être supérieure à zéro.
    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La quantité est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'La quantité doit être supérieure à 0.'
    )]
    private ?int $detail_quantite = null;

    // Le prix unitaire doit être supérieur à zéro.
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le prix unitaire est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le prix unitaire doit être supérieur à 0 €.'
    )]
    private ?string $detail_unitaire = null;

    // Une ligne appartient obligatoirement à une commande.
    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'La commande est obligatoire.'
    )]
    private ?Commande $commande = null;

    // Le produit peut devenir NULL après sa suppression.
    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Produit $produit = null;

    // Le total de la ligne doit être supérieur à zéro.
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le total de la ligne est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le total de la ligne doit être supérieur à 0 €.'
    )]
    private ?string $detail_total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetailQuantite(): ?int
    {
        return $this->detail_quantite;
    }

    public function setDetailQuantite(?int $detail_quantite): static
    {
        $this->detail_quantite = $detail_quantite;
        return $this;
    }

    public function getDetailUnitaire(): ?string
    {
        return $this->detail_unitaire;
    }

    public function setDetailUnitaire(?string $detail_unitaire): static
    {
        $this->detail_unitaire = $detail_unitaire;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function getDetailTotal(): ?string
    {
        return $this->detail_total;
    }

    public function setDetailTotal(?string $detail_total): static
    {
        $this->detail_total = $detail_total;
        return $this;
    }
}